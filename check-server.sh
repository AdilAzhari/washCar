#!/bin/bash
# Server verification script for carwash.aljebal-albeedos.com
# Run this on the SERVER after connecting via SSH

echo "=== Checking Server Project Status ==="
echo ""

# Find the project directory
echo "1. Locating project directory..."
if [ -d "$HOME/public_html/carwash" ]; then
    PROJECT_DIR="$HOME/public_html/carwash"
elif [ -d "$HOME/carwash.aljebal-albeedos.com" ]; then
    PROJECT_DIR="$HOME/carwash.aljebal-albeedos.com"
elif [ -d "$HOME/public_html" ]; then
    PROJECT_DIR="$HOME/public_html"
else
    PROJECT_DIR="$HOME"
fi

echo "Project directory: $PROJECT_DIR"
cd "$PROJECT_DIR" || exit

echo ""
echo "2. Checking Git status..."
git log -1 --oneline
echo ""
git status --short

echo ""
echo "3. Checking for payment confirmation button in Vue files..."
grep -n "Confirm Payment" resources/js/Pages/Queue/WaitingQueue.vue 2>/dev/null || echo "Not found in WaitingQueue.vue"
grep -n "Confirm Payment" resources/js/Pages/Queue/InProgress.vue 2>/dev/null || echo "Not found in InProgress.vue"
grep -n "Confirm Payment" resources/js/Pages/Queue/ViewQueue.vue 2>/dev/null || echo "Not found in ViewQueue.vue"

echo ""
echo "4. Checking frontend build..."
ls -lh public/build/manifest.json 2>/dev/null || echo "Frontend not built (manifest.json missing)"
ls -lh public/build/assets/*.js 2>/dev/null | head -5 || echo "No JS assets found"

echo ""
echo "5. Latest git commits..."
git log -5 --oneline

echo ""
echo "=== Verification Complete ==="
