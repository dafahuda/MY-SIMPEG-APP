# DESIGN.md — Design Direction for MY-SIMPEG-APP

> This file is the **design direction** (the soul of the UI). The anti-slop
> filter lives in `ANTISLOP.md`. UI language: **Bahasa Indonesia** — all UI copy
> is written in polite, concise, official Indonesian (this is a government
> personnel app, not a marketing landing page).
>
> Visual style: **Modern Enterprise SaaS Dashboard** — clean corporate UI, soft
> flat design, light blue-gray interface, compact information density,
> desktop-first, functional and structured, minimal decoration. Think Asana /
> ClickUp / Monday.com density with a softer corporate blue language.

## Identity

**MY-SIMPEG** — Sistem Informasi Kepegawaian ASN. Users are HR staff and civil
servants who open this app **to work** (not to browse). The design must feel
like **rapi, tenang, efisien government office tooling** — informative first,
pretty second.

## Principles

1. **Data-dense, not busy.** List, recap, and report pages show tables and
   numbers. Priority: row legibility, column scan-ability, clear type hierarchy.
2. **Consistent with what exists.** The app already has 100+ pages on established
   patterns (white cards, `bg-gray-50` uppercase table headers, colored status
   badges, solid single-color buttons). **New pages follow existing patterns** —
   never introduce a new style mid-application.
3. **Color is functional, not decorative.** Green = success/approved, yellow =
   needs attention, red = danger/delete, blue = primary action/information,
   purple = special status only. No decorative gradients.
4. **Dark mode stays** (Tailwind `dark:` classes) on all components — tokens
   below define the light theme; dark theme keeps the existing gray scale.

## Design Tokens (light theme)

| Token | Value | Usage |
|---|---|---|
| Primary blue | `#064A9D` | primary actions, active state, links |
| App background | `#EEF5FC` | page background |
| Sidebar background | `#F4F8FD` | left navigation |
| Card surface | `#FFFFFF` | cards, tables, forms |
| Border | `#CAD9E8` | 1px component borders (prefer over shadows) |
| Primary text | `#0A1628` | headings, body, table cells |
| Secondary text | `#53657A` | labels, captions, meta |

Status colors (soft pastel backgrounds only, never large saturated fills —
combine color with text or icon):

- Blue — active
- Green — completed / healthy
- Yellow — review / pending
- Red or pink — overdue / danger
- Purple — special status

**Migration note:** the codebase currently uses Tailwind `blue-600/700`
(`#2563eb`) as the working approximation of the primary blue. When tokens are
wired (CSS custom properties / `@theme`), map primary actions to `#064A9D`.

## Typography

- Inter or a similar modern sans-serif; the current system stack is an
  acceptable approximation — avoid adding heavy font downloads
- Small, compact type scale; medium to semi-bold headings; no oversized type
- Page heading: `text-2xl font-semibold` (existing header pattern)
- Column labels: `text-xs uppercase tracking-wider` in secondary text color
- Identity numbers (NIP, NIK, NPWP): `font-mono`
- Bahasa Indonesia, correct spelling, for every label and message

## Spacing & Radius

- 4px spacing system; 8px icon-to-text; 12–16px component padding;
  16px between cards/sections; avoid excessive whitespace
- Radius: inputs 4px, buttons 4px, cards 4–6px, badges pill, avatars circle
- Migration note: the legacy card pattern uses `rounded-lg` (8px) — tolerable
  on untouched pages, new/touched cards go to 4–6px (`rounded-md`)

## Shadows

- Minimal: **prefer 1px borders over floating shadows**; cards get a very
  subtle shadow or none (`shadow-sm` ceiling, plus the border)
- Stronger shadow (`shadow-lg`/`xl`) only for dropdowns, menus, dialogs, overlays
- Buttons may keep their existing light elevation — affordance, not decoration

## Components (existing patterns — follow them)

| Component | Pattern |
|---|---|
| List page | Title + breadcrumb → flash message → white card with table |
| Status badge | `rounded-full px-2.5 py-0.5 text-xs font-semibold` + semantic color |
| Primary button | corporate blue, 4px radius, compact height, white text |
| Secondary button | white or light blue surface with border, 4px radius |
| Danger button | `text-red-600` + confirmation naming the object being deleted |
| Inputs / dropdowns | white or light blue background, thin blue-gray border, 4px radius, compact height, clear hover **and** focus states |
| Icons | outline style, 14–18px, consistent stroke weight, dark blue or muted gray — the existing inline outline set (Heroicons style) is the house style; do not mix icon libraries |
| Delete confirm | `confirm()` dialog with Indonesian text naming the object |
| Success flash | `bg-green-50 text-green-700` + green border |

## Nada Copy (Bahasa Indonesia)

- Polite and official: "Dokumen berhasil diunggah.", never
  "Awesome! Your doc has landed"
- No marketing buzzwords, no decorative emoji (one small marker max per heading)
- Error messages say what went wrong and how to fix it:
  "Ukuran file maksimal 5 MB.", never "Oops! Something went wrong."

## Dial (target liveliness)

Dial: **ENERGY 1 / RHYTHM 2 / MOTION 1**

Government work app: calm and stable. Animation is only smooth hover/focus
transitions; no entrance animations, no parallax, no confetti. "Life" comes
from accurate data and clear status, not movement.

## Yang Tidak Boleh Terjadi (Avoid)

- Glassmorphism, neumorphism, blob decorations, generic AI illustrations
- Heavy gradients (blue-purple gradients especially), strong card shadows
- Oversized rounded cards, pill-shaped buttons (badges may be pill)
- Large marketing typography, excessive whitespace, highly saturated colors
- Generic AI dashboard styling: eyebrow badges over headlines, bento grids,
  sparkle icons, glowing borders, monospace display headings
- Emoji crowds on buttons/headings (one small marker is enough)
- English copy in the UI ("Submit", "Success!", "Oops")
- Dashboard layouts full of stat cards nobody uses
- Ornate display fonts — use the default sans stack
