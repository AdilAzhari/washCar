<washywashy_design_theme>
This is WashyWashy Pro - a professional car wash management system. The design should feel:
- **Operational & Efficient** - Staff need to manage queues, bays, and customers quickly
- **Clean & Fresh** - Reflects the car wash industry (water, cleanliness, shine)
- **Dashboard-Focused** - Real-time monitoring of operations is critical
- **Multi-User** - Admins, managers, and staff have different needs
- **Mobile-Ready** - Staff use tablets/phones on the floor, customers join queues via mobile

Core Design Principles:
- **Speed matters**: Real-time bay status, instant queue updates, fast customer check-in
- **Clarity matters**: At-a-glance status indicators, color-coded bays, clear queue positions
- **Trust matters**: Professional appearance for B2B software, reliable data display
- **Efficiency matters**: Minimize clicks for common actions, keyboard shortcuts, bulk operations

Color Strategy:
- **Primary (Ocean Blue)**: Trust, water, cleanliness - already using `hsl(199 89% 48%)`
- **Success (Fresh Green)**: Completed washes, available bays - `hsl(160 84% 39%)`
- **Warning (Amber)**: Low inventory, long wait times - `hsl(38 92% 50%)`
- **Error (Coral Red)**: Bay maintenance, failed transactions - `hsl(0 84% 60%)`
- **Neutral (Slate)**: Professional, modern dashboard aesthetic

Industry Inspiration:
- **Service Management**: ServiceTitan, Jobber (field service software)
- **Queue Systems**: Qmatic, QLess (digital queue management)
- **Dashboard Design**: Linear, Vercel (clean, data-dense interfaces)
- **Automotive**: Tesla service centers, premium car wash brands
</washywashy_design_theme>

<use_distinctive_typography>
Typography should balance professionalism with modern service industry aesthetics.

Current Font: Inter (acceptable but generic)

Recommended Upgrades for Car Wash Management:
- **Modern Professional**: **DM Sans** + Space Mono (for queue numbers, bay IDs)
- **Clean & Technical**: **Outfit** + JetBrains Mono (for timers, durations)
- **Premium Service**: **Sora** + IBM Plex Mono (for pricing, metrics)
- **Bold & Efficient**: **Plus Jakarta Sans** + Fira Code (for status codes)

Font Usage:
- **Dashboard Headers**: 700-800 weight, 2rem-3rem (Branch names, page titles)
- **Metrics/Stats**: 600-700 weight, tabular numbers (Revenue, wash counts)
- **Body Text**: 400-500 weight, 1rem (Customer names, package descriptions)
- **Queue Numbers**: Monospace, 800 weight, 3rem+ (highly visible)
- **Bay Status**: 600 weight, 1.25rem (clear at a distance)
- **Timestamps**: 400 weight, 0.875rem (subtle but readable)

Size Scale:
- **xs**: 0.75rem (metadata, timestamps)
- **sm**: 0.875rem (table data, secondary info)
- **base**: 1rem (form labels, descriptions)
- **lg**: 1.125rem (card titles, section headers)
- **xl**: 1.25rem (page titles, bay numbers)
- **2xl**: 1.5rem (dashboard stats)
- **3xl**: 1.875rem (queue numbers, revenue totals)
- **4xl**: 2.25rem+ (hero metrics, bay displays)

Load fonts from Google Fonts or Bunny Fonts (privacy-focused).
</use_distinctive_typography>

<motion_and_interaction>
Animations should feel responsive and professional, not playful.

Critical Animations:
- **Bay Status Change**: Color transition (300ms) + subtle pulse for active state
- **Queue Update**: Slide in new customers (200ms), slide out completed (250ms)
- **Wash Complete**: Success checkmark scale (200ms) + confetti (optional, can disable)
- **Real-time Updates**: Gentle fade-in for new data (150ms)
- **Modal Actions**: Scale + fade (200ms) for dialogs

Performance:
- Use CSS transforms (translate, scale) - GPU accelerated
- Avoid animating width, height, top, left - causes reflow
- Use `will-change` only for active animations
- Prefer Vue Transition API for component changes

Micro-interactions:
- **Button press**: scale(0.98) on active state
- **Card hover**: subtle lift (translateY(-2px) + shadow increase)
- **Status badges**: Pulse animation for "active" status
- **Loading states**: Skeleton screens for tables, shimmer for cards
- **Drag & drop**: Visual feedback for queue reordering

Animation Library (Vue 3 + Inertia):
- Use Vue `<Transition>` for enter/leave
- Use `<TransitionGroup>` for queue lists
- Consider `@vueuse/motion` for complex orchestrations
- Use CSS animations for continuous states (pulsing, rotating)

Timing:
- **Instant feedback**: 0-100ms (button clicks, input focus)
- **Quick transitions**: 150-250ms (modals, dropdowns)
- **Data updates**: 200-300ms (status changes, queue updates)
- **Never exceed 500ms** for operational UI (staff need speed)
</motion_and_interaction>

<color_and_visual_hierarchy>
Colors must communicate operational status clearly and professionally.

Color Psychology for Car Wash Management:
- **Blue (Water/Trust)**: Primary brand, navigation, safe actions
- **Green (Success/Clean)**: Available bays, completed washes, positive metrics
- **Amber (Caution)**: Waiting customers, low stock, attention needed
- **Red (Alert)**: Maintenance required, errors, critical issues
- **Cyan (Active)**: Wash in progress, real-time activity
- **Purple (Premium)**: VIP customers, platinum packages, loyalty tiers

Current Implementation (Good Foundation):
```css
--primary: 199 89% 48%;        /* Ocean blue - keep */
--success: 160 84% 39%;        /* Fresh green - keep */
--warning: 38 92% 50%;         /* Amber - keep */
--destructive: 0 84% 60%;      /* Coral red - keep */
--accent: 160 84% 39%;         /* Currently same as success - could differentiate */
```

Recommended Enhancements:
- **Bay Status Colors**: 
  - Idle: `hsl(215 16% 47%)` (muted gray)
  - Active: `hsl(199 89% 48%)` (primary blue)
  - Maintenance: `hsl(38 92% 50%)` (warning amber)
  - Completed: `hsl(160 84% 39%)` (success green)

- **Queue Status Colors**:
  - Waiting: `hsl(38 92% 50%)` (amber)
  - In Progress: `hsl(199 89% 48%)` (blue)
  - Completed: `hsl(160 84% 39%)` (green)

- **Membership Tiers**:
  - Regular: `hsl(215 16% 47%)` (gray)
  - Silver: `hsl(0 0% 75%)` (silver)
  - Gold: `hsl(45 100% 51%)` (gold)
  - Platinum: `hsl(270 50% 60%)` (purple)

Backgrounds:
- **Dashboard**: Subtle gradient `from-background to-muted` (not flat)
- **Cards**: Clean white/dark with subtle shadows
- **Sidebar**: Dark mode by default for staff terminals
- **Empty States**: Subtle water-themed illustrations

Shadows & Depth:
- **Cards**: Soft shadows for hierarchy `shadow-sm hover:shadow-md`
- **Modals**: Strong shadows for focus `shadow-xl`
- **Active elements**: Colored shadows matching status (blue glow for active bay)
- **Pressed states**: Inner shadows for tactile feedback
</color_and_visual_hierarchy>

<mobile_and_desktop_ux>
Staff use desktop dashboards, customers use mobile for queue joining.

Desktop (Staff/Admin):
- **Dashboard Layout**: Sidebar navigation + main content area
- **Data Density**: Tables with 10-20 rows visible, pagination
- **Keyboard Shortcuts**: Quick actions (N = new customer, Q = queue view, B = bay status)
- **Multi-column**: 2-3 column layouts for metrics, side-by-side comparisons
- **Hover States**: Rich tooltips, preview cards on hover

Mobile (Customer Queue Joining):
- **Touch Targets**: Minimum 44x44px (iOS standard)
- **Single Column**: Stack all content vertically
- **Bottom Navigation**: Primary actions at thumb zone
- **QR Code Scanner**: Large, centered camera viewfinder
- **Queue Status**: Full-screen, auto-refreshing position display

Tablet (Floor Staff):
- **Hybrid Layout**: Simplified dashboard, larger touch targets
- **Quick Actions**: Large buttons for common tasks (start wash, complete, maintenance)
- **Portrait Mode**: Optimized for holding while walking
- **Offline Support**: Cache critical data, sync when connected

Gestures:
- **Pull to Refresh**: Update queue and bay status
- **Swipe Actions**: Complete wash, remove from queue
- **Long Press**: Quick edit customer details
- **Pinch Zoom**: View detailed bay timeline (optional)

Progressive Enhancement:
- **Core functionality**: Works without JavaScript (forms, navigation)
- **Enhanced with Vue**: Real-time updates, smooth transitions
- **Offline Mode**: Service worker for queue status, bay availability
- **Background Sync**: Queue updates when connection restored

Performance Budget:
- **Dashboard Load**: <2s on 4G (staff on tablets)
- **Queue Join**: <1.5s on 3G (customers in parking lot)
- **Real-time Updates**: <500ms latency (WebSocket or polling)
- **Images**: <50KB per customer photo (WebP format)
</mobile_and_desktop_ux>

<component_design_patterns>
Build components specific to car wash operations.

**Bay Status Card**:
- Large bay number (3rem+, bold, monospace)
- Color-coded status indicator (dot + text)
- Current customer name (if active)
- Timer/duration (monospace, tabular numbers)
- Quick action buttons (Start, Complete, Maintenance)
- Progress bar for wash duration

**Queue Item**:
- Queue position (large, bold, left side)
- Customer name + vehicle info (plate number, model)
- Package name + duration estimate
- Wait time indicator (color-coded: green <10min, amber 10-20min, red >20min)
- Swipe actions (assign bay, remove, edit)

**Customer Card**:
- Avatar/initials (circular, color-coded by tier)
- Name + membership tier badge
- Vehicle information (make, model, plate)
- Wash history count (small, subtle)
- Quick actions (add to queue, view history)

**Branch Card**:
- Branch name (bold, large)
- QR code (for customer queue joining)
- Today's stats (revenue, washes, avg wait)
- Bay status summary (3 active, 2 idle, 1 maintenance)
- Staff count indicator

**Package Selector**:
- Color-coded cards (not dropdown)
- Package name + price (large, prominent)
- Duration estimate (icon + time)
- Description (2 lines max)
- Popularity indicator (optional: "Most Popular")

**Transaction Row**:
- Timestamp (left, subtle)
- Customer name (bold)
- Package name (normal weight)
- Amount (right, bold, tabular)
- Status badge (completed, pending, refunded)

**Empty States**:
- Water/car wash themed illustrations
- Clear message ("No customers in queue")
- Primary action ("Add Customer" or "Scan QR Code")
- Helpful tip (e.g., "Customers can join via QR code")

**Notification Toast**:
- Icon (status-specific: checkmark, warning, error)
- Title (bold, 1 line)
- Message (2 lines max)
- Action button (optional: "View", "Undo")
- Auto-dismiss (3-5 seconds)
</component_design_patterns>

<avoid_ai_slop_aesthetic>
Avoid generic SaaS dashboard aesthetics. Make it specific to car wash operations.

❌ Don't:
- Purple/pink gradients (not relevant to car wash industry)
- Glassmorphism everywhere (reduces readability for operational data)
- Generic "user" icons (use car/vehicle icons)
- Overly playful animations (this is professional software)
- Rainbow color schemes (use water/clean/automotive palette)
- Excessive rounded corners (balance modern with professional)
- Stock photos of random people (use car wash imagery)
- Generic "Welcome back!" hero sections (show operational data)

✅ Do:
- **Water-themed accents**: Subtle wave patterns, water droplet icons
- **Automotive iconography**: Car silhouettes, spray nozzles, soap bubbles
- **Status-driven colors**: Clear visual language for bay/queue states
- **Data-dense layouts**: Staff need information, not whitespace
- **Professional typography**: Clean, readable, not overly stylized
- **Contextual illustrations**: Car wash scenes for empty states
- **Functional animations**: Indicate real-time updates, not decoration
- **Industry-specific metrics**: Wash duration, bay utilization, not generic KPIs

Design Checklist:
- ✅ Does it look like car wash management software? (Not generic CRM)
- ✅ Can staff quickly understand bay status from across the room?
- ✅ Are queue numbers large and visible for customers?
- ✅ Do colors clearly communicate operational states?
- ✅ Is the brand personality professional yet approachable?
- ✅ Would this design work for a different industry? (If yes, make it more specific)

**WashyWashy Brand Personality**:
- Clean, modern, efficient
- Trustworthy and professional
- Water/ocean themed (blues, greens, fresh colors)
- Tech-forward but not intimidating
- Service-oriented, customer-focused
</avoid_ai_slop_aesthetic>

<dashboard_specific_patterns>
The dashboard is the heart of WashyWashy Pro. Design for operational efficiency.

**Dashboard Layout**:
- **Top Bar**: Branch selector, notifications, user menu
- **Sidebar**: Navigation (dark mode, always visible on desktop)
- **Main Area**: Metrics grid + primary content
- **Quick Actions**: Floating action button or fixed bottom bar

**Metrics Display**:
- **Stat Cards**: 4-column grid on desktop, 1-column on mobile
  - Large number (3rem, bold, tabular)
  - Label (0.875rem, muted)
  - Trend indicator (↑ 12% from yesterday)
  - Sparkline chart (optional, subtle)

**Real-time Updates**:
- **Bay Status Grid**: Visual grid showing all bays
  - Color-coded squares (green=idle, blue=active, amber=maintenance)
  - Bay number overlay
  - Click to view details
  - Auto-refresh every 5 seconds

**Queue View**:
- **Two-column layout**: Waiting (left) | In Progress (right)
- **Auto-scroll**: New customers slide in at bottom
- **Position numbers**: Large, bold, always visible
- **Drag & drop**: Reorder queue (with confirmation)

**Charts & Analytics**:
- **Revenue Trends**: Line chart, 6-month view
- **Bay Utilization**: Horizontal bar chart
- **Package Popularity**: Donut chart with percentages
- **Peak Hours**: Heatmap (day of week × hour)

**Responsive Behavior**:
- **Desktop**: Multi-column, data-dense
- **Tablet**: 2-column, larger touch targets
- **Mobile**: Single column, prioritize critical info

**Dark Mode**:
- **Sidebar**: Always dark (staff preference)
- **Main Area**: Toggle light/dark
- **Charts**: Adjust colors for dark backgrounds
- **Accessibility**: Maintain WCAG AA contrast ratios
</dashboard_specific_patterns>