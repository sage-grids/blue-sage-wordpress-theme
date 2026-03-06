#!/usr/bin/env bash
#
# Blue Sage WordPress Theme – release helper.
#
# Usage (from repo root):
#   ./.github/scripts/release.sh patch          # 1.0.0 → 1.0.1
#   ./.github/scripts/release.sh minor          # 1.0.0 → 1.1.0
#   ./.github/scripts/release.sh major          # 1.0.0 → 2.0.0
#   ./.github/scripts/release.sh 1.2.0         # explicit version
#
# What it does:
#   1. Reads the current version from the theme header.
#   2. Calculates the next version.
#   3. Updates the Version header in style.css and functions.php.
#   4. Updates the BLUE_SAGE_VERSION constant.
#   5. Promotes [Unreleased] in CHANGELOG.md to the new version + today's date.
#   6. Commits the changes, creates a signed+annotated git tag, and prompts to push.
#
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
THEME_FILE="${ROOT_DIR}/style.css"
FUNCTIONS_FILE="${ROOT_DIR}/functions.php"
CHANGELOG="${ROOT_DIR}/CHANGELOG.md"
REPO_URL="https://github.com/sage-grids/blue-sage-wordpress-theme"

# ── helpers ────────────────────────────────────────────────────────────────────

die() { echo "ERROR: $*" >&2; exit 1; }
info() { echo "  → $*"; }

require_clean_tree() {
    git -C "$ROOT_DIR" diff --quiet HEAD -- \
        || die "Working tree is dirty. Commit or stash your changes first."
}

current_version() {
    grep -oE "Version:[[:space:]]*[0-9]+\.[0-9]+\.[0-9]+" "$THEME_FILE" \
        | grep -oE "[0-9]+\.[0-9]+\.[0-9]+"
}

bump_version() {
    local ver="$1" bump="$2"
    IFS='.' read -r major minor patch <<< "$ver"
    case "$bump" in
        major) echo "$((major + 1)).0.0" ;;
        minor) echo "${major}.$((minor + 1)).0" ;;
        patch) echo "${major}.${minor}.$((patch + 1))" ;;
        [0-9]*.[0-9]*.[0-9]*) echo "$bump" ;;  # explicit version passed as bump arg
        *) die "Invalid bump type '${bump}'. Use patch|minor|major or x.y.z." ;;
    esac
}

validate_semver() {
    [[ "$1" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] \
        || die "'$1' is not a valid semver (x.y.z)."
}

# ── main ───────────────────────────────────────────────────────────────────────

BUMP="${1:-patch}"

require_clean_tree

OLD_VERSION="$(current_version)"
NEW_VERSION="$(bump_version "$OLD_VERSION" "$BUMP")"
validate_semver "$NEW_VERSION"
TODAY="$(date +%Y-%m-%d)"
TAG="v${NEW_VERSION}"

echo ""
echo "Blue Sage WordPress Theme – Release"
echo "────────────────────────────────────"
echo "  Current : v${OLD_VERSION}"
echo "  New     : ${TAG}"
echo "  Date    : ${TODAY}"
echo ""
read -rp "Continue? [y/N] " confirm
[[ "$confirm" =~ ^[Yy]$ ]] || { echo "Aborted."; exit 0; }
echo ""

# 1. Update theme header: "Version: x.y.z" in style.css
info "Updating Version header in style.css"
sed -i '' "s/^Version:.*/Version:            ${NEW_VERSION}/" "$THEME_FILE"

# 2. Update functions.php header and constant
info "Updating functions.php header and BLUE_SAGE_VERSION constant"
sed -i '' "s/ \* @version .*/ * @version ${NEW_VERSION}/" "$FUNCTIONS_FILE"
sed -i '' "s/define( 'BLUE_SAGE_VERSION', '[^']*' )/define( 'BLUE_SAGE_VERSION', '${NEW_VERSION}' )/" "$FUNCTIONS_FILE"

# 3. Update CHANGELOG.md – promote [Unreleased] section
info "Updating CHANGELOG.md"

# Add new versioned entry below the [Unreleased] heading
PREV_TAG="v${OLD_VERSION}"
NEW_ENTRY="## [${NEW_VERSION}] - ${TODAY}"

# Replace the blank [Unreleased] block with [Unreleased] + new versioned header
python3 - "$CHANGELOG" "$NEW_VERSION" "$TODAY" "$REPO_URL" "$OLD_VERSION" <<'PYEOF'
import sys, re, pathlib

path     = pathlib.Path(sys.argv[1])
new_ver  = sys.argv[2]
today    = sys.argv[3]
repo     = sys.argv[4]
old_ver  = sys.argv[5]

text = path.read_text()

# Insert versioned section after [Unreleased] line
unreleased_pat = re.compile(r'(## \[Unreleased\]\n)', re.MULTILINE)
if not unreleased_pat.search(text):
    sys.exit("ERROR: Could not find '## [Unreleased]' in CHANGELOG.md")

replacement = f'\\1\n## [{new_ver}] - {today}\n'
text = unreleased_pat.sub(replacement, text, count=1)

# Update / add comparison links at the bottom
link_unreleased = f'[Unreleased]: {repo}/compare/v{new_ver}...HEAD'
link_new        = f'[{new_ver}]: {repo}/compare/v{old_ver}...v{new_ver}'

# Replace existing [Unreleased] link if present, else append
if re.search(r'^\[Unreleased\]:', text, re.MULTILINE):
    text = re.sub(r'^\[Unreleased\]:.*$', link_unreleased, text, flags=re.MULTILINE)
else:
    text = text.rstrip('\n') + f'\n{link_unreleased}\n'

# Insert new version link after [Unreleased] link
if not re.search(rf'^\[{re.escape(new_ver)}\]:', text, re.MULTILINE):
    text = re.sub(
        r'(^\[Unreleased\]:.*\n)',
        f'\\1{link_new}\n',
        text,
        flags=re.MULTILINE,
    )

path.write_text(text)
print(f"  CHANGELOG.md updated for {new_ver}")
PYEOF

# 4. Commit
info "Staging changed files"
git -C "$ROOT_DIR" add \
    "$THEME_FILE" \
    "$FUNCTIONS_FILE" \
    "$CHANGELOG"

info "Creating release commit"
git -C "$ROOT_DIR" commit -m "chore: release ${TAG}"

# 5. Tag
info "Creating annotated tag ${TAG}"
git -C "$ROOT_DIR" tag -a "$TAG" -m "Release ${TAG}"

echo ""
echo "Done! Tag ${TAG} created locally."
echo ""
read -rp "Push commit + tag to origin? [y/N] " push_confirm
if [[ "$push_confirm" =~ ^[Yy]$ ]]; then
    git -C "$ROOT_DIR" push origin HEAD
    git -C "$ROOT_DIR" push origin "$TAG"
    echo ""
    echo "Pushed. GitHub Actions will build the release zip automatically."
    echo "Track it at: ${REPO_URL}/actions"
else
    echo ""
    echo "Not pushed. When ready, run:"
    echo "  git push origin HEAD && git push origin ${TAG}"
fi
