import { type VariantProps, cva } from 'class-variance-authority'

export const buttonVariants = cva(
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 active:scale-[0.98] [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm hover:shadow-md',
        destructive:
          'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-sm hover:shadow-md',
        outline:
          'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
        secondary:
          'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        ghost: 'hover:bg-accent hover:text-accent-foreground',
        link: 'text-primary underline-offset-4 hover:underline',
        'primary-action': 'bg-bay-active text-bay-active-foreground hover:bg-bay-active/90 shadow-md hover:shadow-lg font-semibold',
        'danger-action': 'bg-bay-maintenance text-bay-maintenance-foreground hover:bg-bay-maintenance/90 shadow-md hover:shadow-lg font-semibold',
      },
      size: {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 rounded-md px-3',
        lg: 'h-11 rounded-md px-8',
        icon: 'h-10 w-10',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  },
)

export type ButtonVariants = VariantProps<typeof buttonVariants>

export const badgeVariants = cva(
  'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default:
          'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        secondary:
          'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
        destructive:
          'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
        outline: 'text-foreground',
        // Bay Status Variants
        'bay-idle': 'border-transparent bg-bay-idle text-bay-idle-foreground',
        'bay-active': 'border-transparent bg-bay-active text-bay-active-foreground animate-pulse-slow',
        'bay-maintenance': 'border-transparent bg-bay-maintenance text-bay-maintenance-foreground',
        'bay-completed': 'border-transparent bg-bay-completed text-bay-completed-foreground',
        // Queue Status Variants
        'queue-waiting': 'border-transparent bg-queue-waiting text-queue-waiting-foreground',
        'queue-in-progress': 'border-transparent bg-queue-in-progress text-queue-in-progress-foreground animate-pulse-slow',
        'queue-completed': 'border-transparent bg-queue-completed text-queue-completed-foreground',
        // Membership Tier Variants
        'tier-regular': 'border-transparent bg-tier-regular text-tier-regular-foreground',
        'tier-silver': 'border-transparent bg-tier-silver text-tier-silver-foreground',
        'tier-gold': 'border-transparent bg-tier-gold text-tier-gold-foreground',
        'tier-platinum': 'border-transparent bg-tier-platinum text-tier-platinum-foreground',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type BadgeVariants = VariantProps<typeof badgeVariants>

export const cardVariants = cva(
  'rounded-lg border bg-card text-card-foreground shadow-sm transition-all duration-200',
  {
    variants: {
      variant: {
        default: '',
        interactive: 'cursor-pointer hover:shadow-md hover:-translate-y-0.5',
        operational: 'border-l-4 hover:shadow-md',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type CardVariants = VariantProps<typeof cardVariants>
