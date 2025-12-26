# Server Sync Instructions for carwash.aljebal-albeedos.com

## Local Project Status
- **Latest Commit:** 2a53346 - feat: add admin dashboard
- **Payment Button Commits:**
  - 8bcaf24 - fix: comprehensive edit modal and queue payment button fixes
  - 987320f - feat: add payment tracking and split queue management
- **Frontend Build:** Dec 25, 18:28 (built and up-to-date)
- **Payment Button Location:**
  - `resources/js/Pages/Queue/InProgress.vue:190`
  - `resources/js/Pages/Queue/ViewQueue.vue:243`
  - `resources/js/Pages/Queue/WaitingQueue.vue:230`

---

## Step 1: Connect to Server

```bash
ssh aljedkrq@aljebal-albeedos.com -p 21098
# Password: iNEtZlFkpGu2
```

---

## Step 2: Run Automated Check Script

Copy and paste this entire block into your SSH terminal:

```bash
#!/bin/bash
echo "=== Server Status Check ==="
echo ""

# Find project directory
if [ -d "$HOME/public_html/carwash" ]; then
    cd "$HOME/public_html/carwash"
elif [ -d "$HOME/carwash.aljebal-albeedos.com" ]; then
    cd "$HOME/carwash.aljebal-albeedos.com"
elif [ -d "$HOME/public_html" ]; then
    cd "$HOME/public_html"
else
    cd "$HOME"
fi

echo "📁 Project Directory: $(pwd)"
echo ""

echo "📝 Latest Commit:"
git log -1 --oneline
echo ""

echo "🔍 Checking for Payment Button:"
grep -n "Confirm Payment" resources/js/Pages/Queue/WaitingQueue.vue && echo "✅ Found in WaitingQueue.vue" || echo "❌ MISSING in WaitingQueue.vue"
grep -n "Confirm Payment" resources/js/Pages/Queue/InProgress.vue && echo "✅ Found in InProgress.vue" || echo "❌ MISSING in InProgress.vue"
grep -n "Confirm Payment" resources/js/Pages/Queue/ViewQueue.vue && echo "✅ Found in ViewQueue.vue" || echo "❌ MISSING in ViewQueue.vue"
echo ""

echo "🏗️ Frontend Build Status:"
if [ -f "public/build/manifest.json" ]; then
    ls -lh public/build/manifest.json
    echo "✅ Frontend built"
else
    echo "❌ Frontend NOT built"
fi
echo ""

echo "📊 Git Status:"
git status --short
echo ""

echo "=== Check Complete ==="
```

---

## Step 3: Diagnose the Issue

Based on the output, you'll see one of these scenarios:

### Scenario A: Code is Missing (Commit not on server)
If server shows commit older than `8bcaf24`:
```bash
# Pull latest code
git pull origin main

# Install dependencies if needed
composer install --no-interaction
npm install
```

### Scenario B: Code Exists but Frontend Not Built
If grep finds "Confirm Payment" but you don't see it on website:
```bash
# Build frontend assets
npm run build

# Or if npm not available, use:
php artisan ziggy:generate
```

### Scenario C: Different Branch
If you're on wrong branch:
```bash
# Check current branch
git branch

# Switch to main
git checkout main
git pull origin main
npm run build
```

---

## Step 4: Verify Fix

After running appropriate fixes:

1. Check the built assets:
```bash
ls -lh public/build/manifest.json
ls -lht public/build/assets/*.js | head -5
```

2. Visit: https://carwash.aljebal-albeedos.com
3. Go to Queue Management page
4. Look for the "💳 Confirm Payment" button on entries with pending payment

---

## Quick Fix (All-in-One)

If you want to just sync everything:

```bash
# Navigate to project
cd ~/public_html/carwash  # Adjust path if needed

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-interaction --no-dev
npm install --production

# Build frontend
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Expected Results

After fixing, you should see the payment confirmation button appearing:
- When an entry has a package assigned
- When payment status is "pending"
- On WaitingQueue, InProgress, and ViewQueue pages
- The button should have green styling and the text "💳 Confirm Payment"

---

## Need Help?

If the button still doesn't appear after following these steps:
1. Check browser console for JavaScript errors
2. Verify the route exists: `php artisan route:list | grep confirm-payment`
3. Check file permissions: `ls -la resources/js/Pages/Queue/`
4. Verify Vite/frontend is properly configured
