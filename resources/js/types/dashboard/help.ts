export interface HelpOnboarding {
    progress: number
    progress_formatted: string
    is_complete: boolean
}

export interface HelpStatus {
    has_clients: boolean
    has_projects: boolean
    has_events: boolean
}

export interface HelpTip {
    id: string
    icon: string
    text: string
    completed: boolean
    route: string
}

export interface HelpAction {
    id: string
    icon: string
    text: string
    variant: 'outline' | 'ghost' | 'default'
    action: string
}

export interface Help {
    onboarding: HelpOnboarding
    status: HelpStatus
    tips: HelpTip[]
    actions: HelpAction[]
}

export interface HelpResponse {
    help: Help
}