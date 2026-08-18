# Client media drop folder

Files placed here are served directly at `/media/<filename>`.

| File | Purpose | Status |
|---|---|---|
| `hero.mp4` | Homepage hero background video | **Awaiting client** |
| `logo.png` | Business logo for the header | **Awaiting client** |

## hero.mp4
Drop the client's video in as `public/media/hero.mp4`. The homepage detects the
file automatically and switches from the blueprint-grid fallback to the video —
no code change required.

Recommended: 1920x1080, H.264 MP4, under 8 MB, 10–20 seconds, silent
(it plays muted and looped).

## House plan images
Plan drawings and renders are **not** stored here. They are uploaded through
Admin → House Plans → Edit → Images, and land in `storage/app/public/plans/{id}/`.
