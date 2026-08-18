# Client media

Files here are served directly at `/media/<filename>`. Everything in this folder
was supplied by the client (August 2026) and ships with the repository, unlike
plan images which admins upload into `storage/app/public/`.

| File | Purpose | Source |
|---|---|---|
| `logo.png` | Header and footer logo | Rasterised at 600 dpi from the client's `logo.ai` vector, not the small 231 px PNG |
| `hero.mp4` | Homepage hero background | 18 s cut from the client's 100 MB / 2 min 33 s original, 720p, audio stripped, 3.0 MB |
| `hero-poster.jpg` | Hero still frame | Shown before the video loads and whenever autoplay is blocked (e.g. iOS Low Power Mode) |
| `projects/*.jpg` | Completed project photos | The client's own published marketing images, resized to 1400 px and recompressed |

Favicons (`/public/favicon.ico`, `favicon-*.png`, `apple-touch-icon.png`) were
generated from the same vector — the central roof-and-window motif, since the
full lockup is unreadable at 16 px.

## Replacing the hero video

Drop a new file in as `hero.mp4`. The homepage detects it automatically. Keep it
short, silent and small — the original 100 MB file would have made the homepage
unusable on mobile data. To re-encode:

```bash
ffmpeg -ss 24 -t 18 -i original.mp4 -an \
  -vf "scale=1280:720:flags=lanczos,fps=24" \
  -c:v libx264 -preset slow -crf 30 -pix_fmt yuv420p \
  -movflags +faststart hero.mp4
```

## Still awaiting from the client

- Real house plan drawings and renders (upload via Admin → House Plans → Edit → Images)
- Genuine customer testimonials — the five currently seeded are placeholder text
