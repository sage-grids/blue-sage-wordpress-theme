#!/bin/bash
# Delete all GitHub releases and tags for the current repository.
# Requires: gh (GitHub CLI), git
# Usage: ./github-reset-releases.sh [--force]

set -e

FORCE=false
if [[ "${1:-}" == "--force" || "${1:-}" == "-f" ]]; then
  FORCE=true
fi

# Check dependencies
if ! command -v gh &>/dev/null; then
  echo "Error: GitHub CLI (gh) is required. Install from https://cli.github.com/"
  exit 1
fi

if ! git rev-parse --git-dir &>/dev/null; then
  echo "Error: Not a git repository."
  exit 1
fi

REMOTE="${GIT_REMOTE:-origin}"
if ! git remote get-url "$REMOTE" &>/dev/null; then
  echo "Error: Remote '$REMOTE' not found."
  exit 1
fi

# Count releases and tags
RELEASE_COUNT=$(gh release list --limit 1000 2>/dev/null | wc -l | tr -d ' ')
TAG_COUNT=$(git ls-remote --tags "$REMOTE" 2>/dev/null | grep -v '\^{}' | wc -l | tr -d ' ')

if [[ "$RELEASE_COUNT" -eq 0 && "$TAG_COUNT" -eq 0 ]]; then
  echo "No releases or tags found. Nothing to delete."
  exit 0
fi

echo "Found: $RELEASE_COUNT release(s), $TAG_COUNT tag(s)"
echo ""
echo "This will permanently delete:"
echo "  - All GitHub releases"
echo "  - All tags (local and remote)"
echo ""

if [[ "$FORCE" != true ]]; then
  read -r -p "Are you sure? (y/N) " response
  if [[ ! "$response" =~ ^[yY]$ ]]; then
    echo "Aborted."
    exit 0
  fi
fi

# 1. Delete all releases (and their tags via --cleanup-tag)
echo ""
echo "Deleting releases..."
while IFS= read -r tag; do
  [[ -z "$tag" ]] && continue
  echo "  Deleting release: $tag"
  gh release delete "$tag" --yes --cleanup-tag 2>/dev/null || true
done < <(gh release list --limit 1000 2>/dev/null | awk '{print $1}')

# 2. Delete any remaining remote tags (tags that weren't releases)
echo ""
echo "Deleting remaining tags..."
while IFS= read -r tag; do
  [[ -z "$tag" ]] && continue
  echo "  Deleting tag: $tag"
  git push "$REMOTE" --delete "$tag" 2>/dev/null || true
done < <(git ls-remote --tags "$REMOTE" 2>/dev/null | grep -v '\^{}' | sed 's/.*refs\/tags\///')

# 3. Delete local tags
echo ""
echo "Deleting local tags..."
for tag in $(git tag -l); do
  echo "  Deleting local tag: $tag"
  git tag -d "$tag" 2>/dev/null || true
done

echo ""
echo "Done. All releases and tags have been deleted."
