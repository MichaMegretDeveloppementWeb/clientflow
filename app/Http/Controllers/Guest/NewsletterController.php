<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\UpdateSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /**
     * Subscribe to the updates newsletter
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255',
            ]);

            $email = $request->input('email');
            
            // Check if subscriber exists before subscribing
            $existingSubscriber = UpdateSubscriber::where('email', $email)->first();
            $wasAlreadyActive = $existingSubscriber && $existingSubscriber->is_active;
            $wasReactivated = $existingSubscriber && !$existingSubscriber->is_active;
            
            $subscriber = UpdateSubscriber::subscribe($email);
            
            // Determine appropriate message
            if ($wasAlreadyActive) {
                $message = 'Vous êtes déjà inscrit à notre liste de diffusion !';
                $messageType = 'already_subscribed';
            } elseif ($wasReactivated) {
                $message = 'Réinscription confirmée ! Vous recevrez à nouveau nos mises à jour.';
                $messageType = 'reactivated';
            } else {
                $message = 'Inscription confirmée ! Vous recevrez désormais nos mises à jour.';
                $messageType = 'new_subscriber';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'type' => $messageType,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez saisir une adresse email valide.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    /**
     * Show unsubscribe page
     */
    public function showUnsubscribe(string $token): View|RedirectResponse
    {
        $subscriber = UpdateSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Lien de désinscription invalide.');
        }

        return view('newsletter.unsubscribe', [
            'subscriber' => $subscriber,
            'token' => $token,
        ]);
    }

    /**
     * Process unsubscription
     */
    public function unsubscribe(string $token): RedirectResponse
    {
        $subscriber = UpdateSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Lien de désinscription invalide.');
        }

        if ($subscriber->is_active) {
            $subscriber->unsubscribe();
            $message = 'Vous avez été désabonné(e) avec succès de notre liste de diffusion.';
        } else {
            $message = 'Vous étiez déjà désabonné(e) de notre liste de diffusion.';
        }

        return redirect()->route('home')->with('success', $message);
    }

    /**
     * Resubscribe using token
     */
    public function resubscribe(string $token): RedirectResponse
    {
        $subscriber = UpdateSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Lien de réinscription invalide.');
        }

        if (!$subscriber->is_active) {
            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);
            $message = 'Vous avez été réinscrit(e) avec succès à notre liste de diffusion.';
        } else {
            $message = 'Vous étiez déjà inscrit(e) à notre liste de diffusion.';
        }

        return redirect()->route('home')->with('success', $message);
    }
}
