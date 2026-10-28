/**
 * Dainik Vishwamitra — Custom Rich-Text Editor v3
 *
 * Professional, zero-dependency rich text editor engineered specifically for news publishing.
 * Built on native contenteditable + DOM manipulation for reliable Devanagari/Hindi and English IME input.
 *
 * Capabilities:
 *  - Full mode: complete newsroom toolbar with undo/redo, headings (H2/H3/H4), quotes, code,
 *    inline styles, semantic <mark> highlight, lists, table builder with Tab cell navigation,
 *    local file upload & drag/drop with shimmer loader, image from URL, figure captions,
 *    live HTML source editor toggle, distraction-free fullscreen mode, word/character/reading-time metrics.
 *  - Lite mode: clean, compact inline-only editor for Key Points.
 *
 * Usage:
 *   window.DvEditor.create(textareaElement, options)
 *   window.DvEditor.createLite(textareaElement, options)
 */

window.DvEditor = (() => {

  // ─────────────────────────────────────────────────────────────────
  //  CSS STYLES
  // ─────────────────────────────────────────────────────────────────

  const CSS = `
.dv-ed {
  border: 1px solid #d6d3d1;
  border-radius: 0.5rem;
  background: #fff;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: border-color .15s, box-shadow .15s;
}
.dv-ed:focus-within {
  border-color: #991b1b;
  box-shadow: 0 0 0 3px rgba(153,27,27,.12);
}

/* Fullscreen mode */
.dv-ed.is-fullscreen {
  position: fixed !important;
  inset: 0 !important;
  z-index: 99999 !important;
  border-radius: 0 !important;
  border: none !important;
  height: 100vh !important;
  max-height: 100vh !important;
  display: flex !important;
  flex-direction: column !important;
  background: #fcfbf9 !important;
}
.dv-ed.is-fullscreen .dv-surface,
.dv-ed.is-fullscreen .dv-html-view {
  max-width: 860px;
  width: 100%;
  margin: 0 auto;
  background: #fff;
  box-shadow: 0 0 20px rgba(0,0,0,.06);
}

/* ── Toolbar ──────────────────────────────────────────────────────── */
.dv-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 2px;
  padding: 6px 8px;
  border-bottom: 1px solid #e7e5e4;
  background: #fafaf9;
  border-radius: 0.5rem 0.5rem 0 0;
  min-height: 42px;
  user-select: none;
}
.dv-ed.is-fullscreen .dv-toolbar {
  border-radius: 0;
  padding: 8px 16px;
  background: #f5f5f4;
  border-bottom: 1px solid #e7e5e4;
}

.dv-tablebar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  padding: 5px 10px;
  border-bottom: 1px solid #fed7aa;
  background: #fff7ed;
  font-size: 12px;
  color: #9a3412;
  user-select: none;
}
.dv-tablebar[hidden] { display: none !important; }

.dv-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  border-radius: 4px;
  cursor: pointer;
  color: #57534e;
  transition: background .12s, color .12s;
  padding: 0;
  flex-shrink: 0;
  position: relative;
}
.dv-btn:hover { background: #f0efee; color: #1c1917; }
.dv-btn.is-active { background: #991b1b; color: #fff; }
.dv-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.dv-btn .material-symbols-outlined {
  font-size: 19px;
  line-height: 1;
  font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20;
}
.dv-btn.is-active .material-symbols-outlined {
  font-variation-settings: 'FILL' 1,'wght' 500,'GRAD' 0,'opsz' 20;
}

/* Tooltips */
.dv-btn[data-tip]:hover::after {
  content: attr(data-tip);
  position: absolute;
  top: calc(100% + 5px);
  left: 50%;
  transform: translateX(-50%);
  background: #1c1917;
  color: #fff;
  font-size: 11px;
  white-space: nowrap;
  padding: 3px 7px;
  border-radius: 4px;
  pointer-events: none;
  z-index: 1000;
  font-family: 'Noto Sans', sans-serif;
  box-shadow: 0 2px 8px rgba(0,0,0,.2);
}

.dv-sep {
  width: 1px;
  height: 22px;
  background: #d6d3d1;
  margin: 0 4px;
  flex-shrink: 0;
}

.dv-count {
  margin-left: auto;
  font-size: 11px;
  color: #78716c;
  white-space: nowrap;
  padding: 0 4px;
  font-variant-numeric: tabular-nums;
  display: flex;
  align-items: center;
  gap: 6px;
}
.dv-reading-time {
  background: #e7e5e4;
  padding: 1px 6px;
  border-radius: 10px;
  font-weight: 500;
  color: #44403c;
}

/* ── Editable Surface ─────────────────────────────────────────────── */
.dv-surface {
  flex: 1;
  min-height: 300px;
  padding: 16px 20px;
  outline: none;
  overflow-y: auto;
  line-height: 1.8;
  font-size: 15px;
  color: #1c1917;
  caret-color: #991b1b;
  font-family: 'Noto Sans', sans-serif;
  position: relative;
}
.dv-surface.lite { min-height: 110px; }

/* Empty state placeholder */
.dv-surface.is-empty::before {
  content: attr(data-placeholder);
  color: #a8a29e;
  pointer-events: none;
  position: absolute;
  left: 20px;
  top: 16px;
  font-size: 15px;
  line-height: 1.8;
}

.dv-surface.is-dropping {
  outline: 2px dashed #991b1b;
  background: #fef2f2;
}

/* HTML Source view */
.dv-html-view {
  flex: 1;
  min-height: 300px;
  padding: 16px 20px;
  outline: none;
  border: none;
  resize: none;
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 13px;
  line-height: 1.6;
  color: #1c1917;
  background: #fafaf9;
  width: 100%;
  box-sizing: border-box;
}

/* Rich content styles inside editor surface */
.dv-surface p { margin: 0 0 0.75em; }
.dv-surface h2 { font-size: 1.45em; font-weight: 700; margin: 0.9em 0 0.35em; line-height: 1.3; color: #1c1917; }
.dv-surface h3 { font-size: 1.2em; font-weight: 700; margin: 0.8em 0 0.3em; line-height: 1.35; color: #292524; }
.dv-surface h4 { font-size: 1.05em; font-weight: 700; margin: 0.65em 0 0.25em; color: #44403c; }

.dv-surface blockquote {
  border-left: 4px solid #991b1b;
  margin: 1em 0;
  padding: 0.6em 1.1em;
  background: #fef2f2;
  border-radius: 0 6px 6px 0;
  font-style: italic;
  color: #44403c;
}

.dv-surface pre {
  background: #1c1917;
  color: #fef3c7;
  padding: 0.85em 1.2em;
  border-radius: 6px;
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
  overflow-x: auto;
  white-space: pre-wrap;
  word-break: break-all;
  margin: 0.8em 0;
}
.dv-surface code {
  background: #f5f5f4;
  color: #991b1b;
  padding: 1px 4px;
  border-radius: 3px;
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
}
.dv-surface pre code {
  background: transparent;
  color: inherit;
  padding: 0;
}

.dv-surface ul, .dv-surface ol { padding-left: 1.6em; margin: 0.4em 0 0.8em; }
.dv-surface li { margin-bottom: 0.25em; }
.dv-surface a { color: #991b1b; text-decoration: underline; }

.dv-surface mark {
  background: #fef08a;
  color: inherit;
  border-radius: 2px;
  padding: 0 3px;
}
.dv-surface hr {
  border: none;
  border-top: 2px solid #e7e5e4;
  margin: 1.2em 0;
}

/* Figures & Images */
.dv-surface figure {
  margin: 1.2em 0;
  text-align: center;
  position: relative;
  transition: all .15s ease;
}
.dv-surface figure.align-left {
  float: left;
  margin: 0.5rem 1.5rem 1rem 0;
  max-width: 50%;
  clear: left;
  text-align: left;
}
.dv-surface figure.align-right {
  float: right;
  margin: 0.5rem 0 1rem 1.5rem;
  max-width: 50%;
  clear: right;
  text-align: right;
}
.dv-surface figure.align-center {
  float: none;
  margin: 1.5rem auto;
  text-align: center;
  clear: both;
  display: block;
  max-width: 100%;
}
.dv-surface figure.align-full {
  float: none;
  width: 100%;
  max-width: 100%;
  margin: 1.5rem 0;
  clear: both;
  text-align: center;
}
.dv-surface figure.align-full img {
  width: 100%;
}
.dv-surface figure img {
  max-width: 100%;
  border-radius: 8px;
  display: inline-block;
  cursor: pointer;
  transition: box-shadow .15s, outline .15s;
}
.dv-surface figure img:hover {
  box-shadow: 0 0 0 3px rgba(153,27,27,.3);
}
.dv-surface figure.dv-figure-selected img {
  outline: 3px solid #991b1b;
  outline-offset: 2px;
  box-shadow: 0 4px 14px rgba(153,27,27,.3);
}
.dv-surface figcaption {
  font-size: 0.825em;
  color: #78716c;
  margin-top: 0.4em;
  font-style: italic;
  outline: none;
  min-height: 1.2em;
}
.dv-surface figcaption.is-empty::before {
  content: attr(data-placeholder);
  color: #a8a29e;
  pointer-events: none;
}
.dv-surface::after {
  content: "";
  display: table;
  clear: both;
}

/* Floating Image Bubble Toolbar */
.dv-image-bubble {
  position: absolute;
  background: #1c1917;
  color: #fff;
  border-radius: 8px;
  padding: 4px;
  display: none;
  align-items: center;
  gap: 2px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5), 0 8px 10px -6px rgba(0,0,0,0.4);
  z-index: 100;
  pointer-events: auto;
  user-select: none;
  white-space: nowrap;
}
.dv-bubble-btn {
  background: transparent;
  border: none;
  color: #d6d3d1;
  padding: 5px 8px;
  border-radius: 5px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 500;
  transition: all .15s ease;
  line-height: 1;
}
.dv-bubble-btn .material-symbols-outlined {
  font-size: 16px;
}
.dv-bubble-btn:hover {
  background: #292524;
  color: #fff;
}
.dv-bubble-btn.is-active {
  background: #991b1b;
  color: #fff;
}
.dv-bubble-btn-danger:hover {
  background: #dc2626;
  color: #fff;
}
.dv-bubble-sep {
  width: 1px;
  height: 16px;
  background: #44403c;
  margin: 0 3px;
}

/* Tables */
.dv-surface table {
  border-collapse: collapse;
  width: 100%;
  margin: 1.2em 0;
  font-size: 0.9em;
}
.dv-surface th, .dv-surface td {
  border: 1px solid #d6d3d1;
  padding: 0.5em 0.8em;
  vertical-align: top;
  min-width: 60px;
}
.dv-surface th {
  background: #fafaf9;
  font-weight: 600;
  text-align: left;
}

/* ── Status Bar ─────────────────────────────────────────────────────── */
.dv-status {
  font-size: 11px;
  padding: 4px 14px;
  color: #57534e;
  border-top: 1px solid #e7e5e4;
  min-height: 24px;
  border-radius: 0 0 0.5rem 0.5rem;
  display: flex;
  align-items: center;
  transition: background .15s, color .15s;
}
.dv-status[hidden] { display: none !important; }
.dv-status[data-tone="error"] { background: #fef2f2; color: #991b1b; }
.dv-status[data-tone="success"] { background: #f0fdf4; color: #166534; }

/* ── Modal Overlay ──────────────────────────────────────────────────── */
.dv-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.5);
  backdrop-filter: blur(2px);
  z-index: 100000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: dv-fade .12s ease;
}
@keyframes dv-fade { from { opacity: 0; } to { opacity: 1; } }

.dv-modal {
  background: #fff;
  border-radius: 0.75rem;
  box-shadow: 0 25px 60px rgba(0,0,0,.28);
  width: 100%;
  max-width: 440px;
  animation: dv-up .15s ease;
}
@keyframes dv-up { from { transform: translateY(12px); } to { transform: translateY(0); } }

.dv-modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1.1rem 0.7rem;
  border-bottom: 1px solid #e7e5e4;
}
.dv-modal-title { font-size: 0.95rem; font-weight: 700; margin: 0; color: #1c1917; }
.dv-modal-close {
  background: none; border: none; cursor: pointer; color: #78716c;
  display: inline-flex; align-items: center; justify-content: center;
  width: 28px; height: 28px; border-radius: 4px; padding: 0;
}
.dv-modal-close:hover { background: #f0efee; color: #1c1917; }

.dv-modal-body { padding: 1rem 1.1rem; display: flex; flex-direction: column; gap: 0.75rem; }
.dv-modal-foot {
  display: flex; justify-content: flex-end; gap: 0.5rem;
  padding: 0.7rem 1.1rem 0.9rem; border-top: 1px solid #e7e5e4;
}

.dv-field { display: flex; flex-direction: column; gap: 0.35rem; }
.dv-field-label { font-size: 0.78rem; font-weight: 600; color: #44403c; }
.dv-field input, .dv-field textarea, .dv-field select {
  width: 100%; padding: 0.5rem 0.75rem;
  border: 1px solid #d6d3d1; border-radius: 0.375rem;
  font-size: 0.875rem; outline: none; font-family: inherit; box-sizing: border-box;
}
.dv-field input:focus, .dv-field textarea:focus, .dv-field select:focus {
  border-color: #991b1b;
  box-shadow: 0 0 0 2px rgba(153,27,27,.12);
}
.dv-check-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #44403c; cursor: pointer; }
.dv-check-row input[type=checkbox] { width: 16px; height: 16px; accent-color: #991b1b; cursor: pointer; }
.dv-hint { font-size: 0.75rem; color: #a8a29e; margin: 0; }

.dv-btn-cancel {
  padding: 0.45rem 1rem; border: 1px solid #d6d3d1; background: #fff;
  border-radius: 0.375rem; font-size: 0.85rem; cursor: pointer; font-family: inherit; color: #57534e;
}
.dv-btn-cancel:hover { background: #fafaf9; color: #1c1917; }
.dv-btn-primary {
  padding: 0.45rem 1.25rem; background: #991b1b; color: #fff; border: none;
  border-radius: 0.375rem; font-size: 0.85rem; cursor: pointer; font-family: inherit; font-weight: 600;
}
.dv-btn-primary:hover { background: #7f1d1d; }

/* ── Table Size Picker Popover ────────────────────────────────────── */
.dv-tpicker {
  position: absolute;
  background: #fff;
  border: 1px solid #d6d3d1;
  border-radius: 8px;
  box-shadow: 0 8px 30px rgba(0,0,0,.18);
  padding: 10px;
  z-index: 100000;
  user-select: none;
}
.dv-tpicker-grid {
  display: grid;
  grid-template-columns: repeat(8, 22px);
  gap: 3px;
}
.dv-tpicker-cell {
  width: 22px; height: 22px;
  border: 1px solid #e7e5e4; border-radius: 3px;
  cursor: pointer; background: #fafaf9;
  transition: background .08s, border-color .08s;
}
.dv-tpicker-cell.hl { background: #fee2e2; border-color: #991b1b; }
.dv-tpicker-label {
  text-align: center;
  font-size: 11px;
  font-weight: 600;
  color: #57534e;
  margin-top: 6px;
}

/* ── Image Upload Shimmer ─────────────────────────────────────────── */
.dv-img-uploading {
  display: inline-block;
  width: 140px; height: 90px;
  background: linear-gradient(90deg, #f5f5f4 25%, #e7e5e4 50%, #f5f5f4 75%);
  background-size: 200% 100%;
  animation: dv-shimmer 1.2s infinite;
  border-radius: 6px;
  vertical-align: middle;
}
@keyframes dv-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
`

  let cssInjected = false
  function injectCss () {
    if (cssInjected) return
    cssInjected = true
    const s = document.createElement('style')
    s.id = 'dv-editor-styles'
    s.textContent = CSS
    document.head.appendChild(s)
  }

  // ─────────────────────────────────────────────────────────────────
  //  DOM & Selection Helpers
  // ─────────────────────────────────────────────────────────────────

  function icon (name) {
    const s = document.createElement('span')
    s.className = 'material-symbols-outlined'
    s.setAttribute('aria-hidden', 'true')
    s.textContent = name
    return s
  }

  function sep () {
    const s = document.createElement('span')
    s.className = 'dv-sep'
    s.setAttribute('aria-hidden', 'true')
    return s
  }

  const BLOCK_TAGS = ['P','H2','H3','H4','BLOCKQUOTE','PRE','LI','FIGCAPTION','FIGURE']
  const ALLOWED_INLINE = new Set(['B','STRONG','I','EM','U','S','A','SUP','SUB','MARK','BR','CODE'])

  function setFigureAlign (fig, align) {
    if (!fig) return
    fig.classList.remove('align-left', 'align-right', 'align-center', 'align-full')
    const img = fig.querySelector('img')
    if (align === 'left') {
      fig.classList.add('align-left')
      fig.style.float = 'left'
      fig.style.margin = '0.5rem 1.5rem 1rem 0'
      fig.style.maxWidth = '50%'
      fig.style.clear = 'left'
      fig.style.textAlign = 'left'
      fig.style.display = 'block'
      if (img) img.style.removeProperty('width')
    } else if (align === 'right') {
      fig.classList.add('align-right')
      fig.style.float = 'right'
      fig.style.margin = '0.5rem 0 1rem 1.5rem'
      fig.style.maxWidth = '50%'
      fig.style.clear = 'right'
      fig.style.textAlign = 'right'
      fig.style.display = 'block'
      if (img) img.style.removeProperty('width')
    } else if (align === 'center') {
      fig.classList.add('align-center')
      fig.style.float = 'none'
      fig.style.margin = '1.5rem auto'
      fig.style.maxWidth = '100%'
      fig.style.clear = 'both'
      fig.style.textAlign = 'center'
      fig.style.display = 'block'
      if (img) img.style.removeProperty('width')
    } else if (align === 'full') {
      fig.classList.add('align-full')
      fig.style.float = 'none'
      fig.style.margin = '1.5rem 0'
      fig.style.width = '100%'
      fig.style.maxWidth = '100%'
      fig.style.clear = 'both'
      fig.style.textAlign = 'center'
      fig.style.display = 'block'
      if (img) img.style.width = '100%'
    }
  }

  function getFigureAlign (fig) {
    if (!fig) return 'center'
    if (fig.classList.contains('align-left') || fig.style.float === 'left') return 'left'
    if (fig.classList.contains('align-right') || fig.style.float === 'right') return 'right'
    if (fig.classList.contains('align-full') || fig.style.width === '100%') return 'full'
    return 'center'
  }

  function execCmd (cmd, val, surface) {
    surface.focus()
    document.execCommand(cmd, false, val ?? null)
  }

  function closestEl (node, root) {
    let n = node
    while (n && n !== root) {
      if (n.nodeType === 1) return n
      n = n.parentNode
    }
    return n === root ? root : null
  }

  function hasTag (el, tags) {
    let n = el
    while (n && n.nodeType === 1) {
      if (tags.includes(n.tagName)) return true
      n = n.parentNode
    }
    return false
  }

  function currentBlock (surface) {
    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) return null
    let n = closestEl(sel.anchorNode, surface)
    while (n && n !== surface) {
      if (BLOCK_TAGS.includes(n.tagName)) return n
      n = n.parentNode
    }
    return null
  }

  function queryState (surface, selectedFigure = null) {
    if (selectedFigure && selectedFigure.isConnected) {
      return {
        active:      true,
        bold:        false,
        italic:      false,
        underline:   false,
        strike:      false,
        code:        false,
        superscript: false,
        subscript:   false,
        highlight:   false,
        link:        null,
        linkTarget:  '',
        block:       'FIGURE',
        inList:      false,
        listType:    null,
        inQuote:     false,
        inFigure:    true,
        inTable:     false,
        align:       getFigureAlign(selectedFigure),
      }
    }

    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) return { active: false }
    const node = closestEl(sel.anchorNode, surface)
    if (!node) return { active: false }
    const listEl = node.closest?.('ul,ol')
    const anchor = node.closest?.('a')
    const fig = node.closest?.('figure')

    let align = getComputedStyle(node).textAlign
    if (fig) {
      align = getFigureAlign(fig)
    }

    return {
      active:      true,
      bold:        hasTag(node, ['B','STRONG']),
      italic:      hasTag(node, ['I','EM']),
      underline:   hasTag(node, ['U','INS']),
      strike:      hasTag(node, ['S','STRIKE','DEL']),
      code:        hasTag(node, ['CODE','PRE']),
      superscript: hasTag(node, ['SUP']),
      subscript:   hasTag(node, ['SUB']),
      highlight:   hasTag(node, ['MARK']) || (node.style && node.style.backgroundColor && node.style.backgroundColor !== 'transparent'),
      link:        anchor ? anchor.getAttribute('href') : null,
      linkTarget:  anchor ? (anchor.getAttribute('target') || '') : '',
      block:       node.tagName,
      inList:      !!listEl,
      listType:    listEl ? listEl.tagName.toLowerCase() : null,
      inQuote:     hasTag(node, ['BLOCKQUOTE']),
      inFigure:    !!fig,
      inTable:     !!node.closest?.('table'),
      align:       align,
    }
  }

  function replaceBlock (block, tag) {
    if (block.tagName === 'FIGURE') return
    const el = document.createElement(tag)
    if (tag === 'PRE') {
      el.textContent = block.innerText
    } else {
      while (block.firstChild) el.appendChild(block.firstChild)
    }
    if (block.style.textAlign) el.style.textAlign = block.style.textAlign
    block.replaceWith(el)

    const r = document.createRange()
    r.selectNodeContents(el)
    r.collapse(false)
    const sel = window.getSelection()
    sel.removeAllRanges()
    sel.addRange(r)
  }

  function toggleBlock (tag, surface) {
    const block = currentBlock(surface)
    if (block && block.tagName === 'FIGURE') return
    if (!block) {
      if (tag === 'PRE') {
        const pre = document.createElement('pre')
        const sel = window.getSelection()
        if (sel && sel.rangeCount) {
          const range = sel.getRangeAt(0)
          pre.textContent = range.toString()
          range.deleteContents()
          range.insertNode(pre)
        }
      } else {
        execCmd('formatBlock', `<${tag.toLowerCase()}>`, surface)
      }
      return
    }
    replaceBlock(block, block.tagName === tag ? 'P' : tag)
  }

  function setAlign (align, surface, selectedFigure = null) {
    const fig = selectedFigure || (currentBlock(surface)?.tagName === 'FIGURE' ? currentBlock(surface) : currentBlock(surface)?.closest?.('figure'))
    if (fig) {
      setFigureAlign(fig, align)
      return
    }
    const block = currentBlock(surface)
    if (block) {
      block.style.textAlign = align === 'left' ? '' : align
    } else {
      execCmd('justify' + align.charAt(0).toUpperCase() + align.slice(1), null, surface)
    }
  }

  /**
   * Semantic Highlight Toggle using <mark>.
   * If inside <mark>, unwraps it.
   * If selection exists, wraps in <mark>.
   */
  function toggleHighlight (surface) {
    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) return
    const node = closestEl(sel.anchorNode, surface)
    const mark = node?.closest?.('mark')

    if (mark) {
      // Unwrap existing mark
      const parent = mark.parentNode
      while (mark.firstChild) parent.insertBefore(mark.firstChild, mark)
      mark.remove()
      return
    }

    if (sel.isCollapsed) return

    const range = sel.getRangeAt(0)
    const el = document.createElement('mark')
    try {
      range.surroundContents(el)
    } catch {
      // If selection spans multiple block elements, fallback to execCommand hilite
      execCmd('hiliteColor', '#fef08a', surface)
    }
  }

  /**
   * Clear Formatting on selection.
   */
  function clearFormatting (surface) {
    execCmd('removeFormat', null, surface)
    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) return
    const node = closestEl(sel.anchorNode, surface)
    // Also strip mark and custom colored spans in selection
    surface.querySelectorAll('mark, span[style]').forEach(el => {
      if (sel.containsNode(el, true)) {
        el.replaceWith(...el.childNodes)
      }
    })
  }

  /**
   * Apply or update a link.
   */
  function applyLink (href, target, surface) {
    const sel = window.getSelection()
    const anchor = sel && sel.rangeCount ? closestEl(sel.anchorNode, surface)?.closest?.('a') : null

    if (!href) {
      if (anchor) {
        anchor.replaceWith(...anchor.childNodes)
      } else {
        execCmd('unlink', null, surface)
      }
      return
    }

    if (anchor && sel.isCollapsed) {
      anchor.setAttribute('href', href)
      anchor.setAttribute('rel', 'noopener noreferrer')
      if (target === '_blank') anchor.setAttribute('target', '_blank')
      else anchor.removeAttribute('target')
      return
    }

    execCmd('createLink', href, surface)
    const newAnchor = sel && sel.rangeCount ? closestEl(sel.anchorNode, surface)?.closest?.('a') : null
    if (newAnchor) {
      newAnchor.setAttribute('rel', 'noopener noreferrer')
      if (target === '_blank') newAnchor.setAttribute('target', '_blank')
      else newAnchor.removeAttribute('target')
    }
  }

  function insertBlock (node, surface) {
    surface.focus()
    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) { surface.appendChild(node); return }

    const block = currentBlock(surface)
    if (block) {
      block.after(node)
      const p = document.createElement('p')
      p.appendChild(document.createElement('br'))
      node.after(p)
      const r = document.createRange()
      const cap = node.tagName === 'FIGURE' ? node.querySelector('figcaption') : node
      r.selectNodeContents(cap ?? node)
      r.collapse(false)
      sel.removeAllRanges()
      sel.addRange(r)
      return
    }

    surface.appendChild(node)
    const p = document.createElement('p')
    p.appendChild(document.createElement('br'))
    surface.appendChild(p)
    const r = document.createRange()
    r.selectNodeContents(p)
    r.collapse(false)
    sel.removeAllRanges()
    sel.addRange(r)
  }

  function insertFigure (src, alt, caption, surface) {
    const fig = document.createElement('figure')
    setFigureAlign(fig, 'center')
    const img = document.createElement('img')
    img.src = src
    img.alt = alt || ''
    img.loading = 'lazy'

    const cap = document.createElement('figcaption')
    cap.textContent = caption || ''
    cap.setAttribute('data-placeholder', 'चित्र का कैप्शन · Image caption…')

    fig.append(img, cap)
    insertBlock(fig, surface)
    return fig
  }

  function buildTable (rows, cols) {
    const tbl = document.createElement('table')
    const body = document.createElement('tbody')
    for (let r = 0; r < rows; r++) {
      const tr = document.createElement('tr')
      for (let c = 0; c < cols; c++) {
        const cell = document.createElement(r === 0 ? 'th' : 'td')
        const p = document.createElement('p')
        p.appendChild(document.createElement('br'))
        cell.appendChild(p)
        tr.appendChild(cell)
      }
      body.appendChild(tr)
    }
    tbl.appendChild(body)
    return tbl
  }

  function modifyTable (axis, action, surface) {
    const sel = window.getSelection()
    if (!sel || !sel.rangeCount) return
    const cell = closestEl(sel.anchorNode, surface)?.closest?.('td,th')
    if (!cell) return
    const row = cell.parentElement
    const table = row.closest('table')
    if (!table) return

    if (axis === 'table' && action === 'delete') {
      const nextP = document.createElement('p')
      nextP.appendChild(document.createElement('br'))
      table.replaceWith(nextP)
      const r = document.createRange()
      r.selectNodeContents(nextP)
      r.collapse(false)
      sel.removeAllRanges()
      sel.addRange(r)
      return
    }

    if (axis === 'row') {
      if (action === 'add') {
        const newRow = document.createElement('tr')
        for (let i = 0; i < row.cells.length; i++) {
          const td = document.createElement('td')
          const p = document.createElement('p')
          p.appendChild(document.createElement('br'))
          td.appendChild(p)
          newRow.appendChild(td)
        }
        row.after(newRow)
      } else {
        const body = table.tBodies[0] ?? table
        if (body.rows.length > 1) row.remove()
      }
      return
    }

    const idx = [...row.cells].indexOf(cell)
    if (action === 'add') {
      table.querySelectorAll('tr').forEach(tr => {
        const td = document.createElement('td')
        const p = document.createElement('p')
        p.appendChild(document.createElement('br'))
        td.appendChild(p)
        tr.children[idx]?.after(td)
      })
    } else {
      if (row.cells.length > 1) {
        table.querySelectorAll('tr').forEach(tr => tr.children[idx]?.remove())
      }
    }
  }

  // ─────────────────────────────────────────────────────────────────
  //  HTML Cleaning on Paste
  // ─────────────────────────────────────────────────────────────────

  function cleanPastedHtml (html) {
    const div = document.createElement('div')
    div.innerHTML = html

    div.querySelectorAll('script,style,meta,link,[class*="MsoNormal"],[style*="mso-"]').forEach(el => el.remove())

    function walk (node) {
      const children = [...node.childNodes]
      children.forEach(child => {
        if (child.nodeType === 1) {
          walk(child)
          const tag = child.tagName
          if (tag !== 'A') {
            child.removeAttribute('class')
            child.removeAttribute('id')
            child.removeAttribute('style')
          }
          if (!ALLOWED_INLINE.has(tag) && !['P','H2','H3','H4','BLOCKQUOTE','UL','OL','LI','TABLE','THEAD','TBODY','TR','TH','TD','FIGURE','IMG','FIGCAPTION','HR','PRE'].includes(tag)) {
            child.replaceWith(...child.childNodes)
          }
        }
      })
    }
    walk(div)
    return div.innerHTML
  }

  // ─────────────────────────────────────────────────────────────────
  //  Dialogs (Link, Image, Table Size)
  // ─────────────────────────────────────────────────────────────────

  const FOCUSABLE = 'a[href],button:not([disabled]),input,select,textarea'

  function openDialog ({ title, body = '', onMount, onSubmit, submitLabel = 'Save', cancelLabel = 'Cancel' }) {
    return new Promise(resolve => {
      const overlay = document.createElement('div')
      overlay.className = 'dv-overlay'

      const modal = document.createElement('div')
      modal.className = 'dv-modal'
      modal.setAttribute('role', 'dialog')
      modal.setAttribute('aria-modal', 'true')
      modal.setAttribute('aria-label', title)
      modal.innerHTML = `
        <div class="dv-modal-head">
          <h2 class="dv-modal-title"></h2>
          <button type="button" class="dv-modal-close" aria-label="बंद करें">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
          </button>
        </div>
        <div class="dv-modal-body"></div>
        <div class="dv-modal-foot">
          <button type="button" class="dv-btn-cancel"></button>
          <button type="button" class="dv-btn-primary"></button>
        </div>
      `
      modal.querySelector('.dv-modal-title').textContent = title
      modal.querySelector('.dv-modal-body').innerHTML = body
      modal.querySelector('.dv-btn-primary').textContent = submitLabel
      modal.querySelector('.dv-btn-cancel').textContent = cancelLabel
      overlay.appendChild(modal)
      document.body.appendChild(overlay)

      const prev = document.activeElement

      const close = result => {
        overlay.remove()
        document.removeEventListener('keydown', onKey, true)
        prev?.focus?.()
        resolve(result)
      }

      const onKey = e => {
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); close(false); return }
        if (e.key === 'Tab') {
          const els = [...modal.querySelectorAll(FOCUSABLE)].filter(el => el.offsetParent !== null)
          if (!els.length) return
          if (e.shiftKey && document.activeElement === els[0]) { e.preventDefault(); els[els.length-1].focus() }
          else if (!e.shiftKey && document.activeElement === els[els.length-1]) { e.preventDefault(); els[0].focus() }
        }
      }

      document.addEventListener('keydown', onKey, true)
      modal.querySelector('.dv-modal-close').onclick = () => close(false)
      modal.querySelector('.dv-btn-cancel').onclick = () => close(false)
      overlay.addEventListener('mousedown', e => { if (e.target === overlay) close(false) })
      modal.querySelector('.dv-btn-primary').onclick = () => {
        if (onSubmit && onSubmit(modal) === false) return
        close(true)
      }

      if (onMount) {
        onMount(modal, close)
      }

      const first = modal.querySelector('input,textarea,select')
      ;(first ?? modal.querySelector('.dv-btn-primary')).focus()
      first?.select?.()
    })
  }

  async function promptLink (current = '', currentTarget = '') {
    let value = '', newTarget = ''
    const ok = await openDialog({
      title: 'लिंक जोड़ें / संपादित करें · Add or Edit Link',
      submitLabel: 'लगाएँ · Apply',
      cancelLabel: 'रद्द · Cancel',
      body: `
        <label class="dv-field">
          <span class="dv-field-label">वेब पता · URL</span>
          <input type="url" name="href" placeholder="https://example.com"
                 value="${current.replace(/"/g, '&quot;')}" autocomplete="off"/>
          <p class="dv-hint">खाली छोड़ने पर लिंक हट जाएगा। (Leave blank to remove)</p>
        </label>
        <label class="dv-check-row">
          <input type="checkbox" name="newtab" ${currentTarget === '_blank' ? 'checked' : ''}/>
          <span>नए टैब में खोलें · Open in new tab</span>
        </label>
      `,
      onSubmit (dlg) {
        value = dlg.querySelector('[name="href"]').value.trim()
        newTarget = dlg.querySelector('[name="newtab"]').checked ? '_blank' : ''
      },
    })
    if (!ok) return null
    if (!value) return { href: '', target: '' }
    if (!/^[a-z][a-z0-9+.-]*:/i.test(value) && !value.startsWith('/')) value = `https://${value}`
    return { href: value, target: newTarget }
  }

  async function promptImageMeta (currentAlt = '', currentCap = '') {
    let alt = '', caption = ''
    const ok = await openDialog({
      title: 'चित्र का विवरण · Image Details',
      submitLabel: 'सहेजें · Save',
      cancelLabel: 'रद्द · Cancel',
      body: `
        <label class="dv-field">
          <span class="dv-field-label">वैकल्पिक विवरण · Alt Text</span>
          <input type="text" name="alt" value="${currentAlt.replace(/"/g,'&quot;')}" autocomplete="off" placeholder="चित्र का विवरण..."/>
          <p class="dv-hint">सर्च इंजन और स्क्रीन रीडर के लिए आवश्यक।</p>
        </label>
        <label class="dv-field">
          <span class="dv-field-label">कैप्शन · Caption</span>
          <input type="text" name="caption" value="${currentCap.replace(/"/g,'&quot;')}" autocomplete="off" placeholder="लेख में चित्र के नीचे दिखाई देने वाला पाठ..."/>
        </label>
      `,
      onSubmit (dlg) {
        alt = dlg.querySelector('[name="alt"]').value.trim()
        caption = dlg.querySelector('[name="caption"]').value.trim()
      },
    })
    if (!ok) return null
    return { alt, caption }
  }

  async function promptImageDialog (uploadOpts = {}) {
    let mode = 'upload'
    let selectedFile = null
    let selectedStorageUrl = null
    let url = '', alt = '', caption = ''

    const ok = await openDialog({
      title: 'चित्र जोड़ें / अपलोड करें · Insert / Upload Image',
      submitLabel: 'जोड़ें · Insert',
      cancelLabel: 'रद्द · Cancel',
      body: `
        <div style="display:flex;gap:6px;margin-bottom:14px;border-bottom:1px solid #e7e5e4;padding-bottom:10px;">
          <button type="button" id="dv-tab-upload-btn" style="flex:1;font-size:12px;font-weight:600;padding:7px 10px;border-radius:6px;border:none;background:#991b1b;color:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;">
            <span class="material-symbols-outlined" style="font-size:16px;">cloud_upload</span>
            <span>कंप्यूटर से फ़ाइल · Upload</span>
          </button>
          <button type="button" id="dv-tab-storage-btn" style="flex:1;font-size:12px;font-weight:600;padding:7px 10px;border-radius:6px;border:1px solid #d6d3d1;background:#fff;color:#44403c;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;">
            <span class="material-symbols-outlined" style="font-size:16px;">perm_media</span>
            <span>स्टोरेज गैलरी · Storage</span>
          </button>
          <button type="button" id="dv-tab-url-btn" style="flex:1;font-size:12px;font-weight:600;padding:7px 10px;border-radius:6px;border:1px solid #d6d3d1;background:#fff;color:#44403c;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;">
            <span class="material-symbols-outlined" style="font-size:16px;">link</span>
            <span>वेब URL · URL</span>
          </button>
        </div>

        <!-- 1. Device Upload Pane -->
        <div id="dv-pane-upload">
          <div id="dv-modal-dropzone" style="position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 14px;border:2px dashed #d6d3d1;border-radius:8px;background:#fafaf9;cursor:pointer;text-align:center;transition:all .15s ease;">
            <span class="material-symbols-outlined" style="font-size:38px;color:#991b1b;margin-bottom:6px;">add_photo_alternate</span>
            <span style="font-size:13px;font-weight:600;color:#1c1917;">चित्र चुनें या यहाँ खींचें · Choose file or drag here</span>
            <span style="font-size:11px;color:#78716c;margin-top:2px;">JPEG, PNG, WebP, GIF · अधिकतम 10MB</span>
            <label style="position:relative;overflow:hidden;margin-top:12px;background:#991b1b;color:#fff;border:none;padding:9px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 1px 3px rgba(0,0,0,0.15);">
              <span class="material-symbols-outlined" style="font-size:18px;">folder_open</span>
              <span>फ़ाइल चुनें · Browse Files</span>
              <input type="file" id="dv-modal-file-input" accept="image/jpeg,image/png,image/webp,image/gif" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:100px;display:block;margin:0;padding:0;border:none;"/>
            </label>
          </div>

          <div id="dv-file-preview-card" style="display:none;margin-top:10px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;align-items:center;gap:10px;">
            <img id="dv-file-thumb" src="" alt="Preview" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #86efac;"/>
            <div style="flex:1;min-width:0;">
              <div id="dv-file-name-text" style="font-size:12px;font-weight:600;color:#166534;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
              <div id="dv-file-size-text" style="font-size:11px;color:#15803d;margin-top:2px;"></div>
            </div>
            <label style="position:relative;overflow:hidden;background:#fff;border:1px solid #bbf7d0;color:#166534;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;">
              <span>बदलें · Change</span>
              <input type="file" id="dv-file-change-input" accept="image/jpeg,image/png,image/webp,image/gif" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:100px;display:block;margin:0;padding:0;border:none;"/>
            </label>
          </div>
        </div>

        <!-- 2. Storage Media Manager Pane -->
        <div id="dv-pane-storage" style="display:none;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:12px;color:#78716c;">स्टोरेज / मीडिया लाइब्रेरी से चित्र चुनें · Select from storage:</span>
            <button type="button" id="dv-storage-refresh-btn" style="background:none;border:none;color:#991b1b;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:3px;font-weight:600;">
              <span class="material-symbols-outlined" style="font-size:15px;">refresh</span>
              <span>रिफ्रेश</span>
            </button>
          </div>
          <div id="dv-storage-loading" style="padding:30px 10px;text-align:center;color:#78716c;font-size:12px;">
            स्टोरेज से चित्र लोड हो रहे हैं… · Loading images…
          </div>
          <div id="dv-storage-empty" style="display:none;padding:30px 10px;text-align:center;color:#78716c;font-size:12px;">
            स्टोरेज में अभी कोई चित्र नहीं है। कंप्यूटर से अपलोड करें।
          </div>
          <div id="dv-storage-grid" style="display:none;grid-template-columns:repeat(auto-fill, minmax(80px, 1fr));gap:8px;max-height:200px;overflow-y:auto;padding:6px;border:1px solid #e7e5e4;border-radius:8px;background:#fafaf9;">
          </div>
          <div id="dv-storage-selected-info" style="display:none;margin-top:8px;padding:6px 10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;color:#1e40af;font-size:12px;font-weight:600;align-items:center;gap:6px;">
            <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
            <span id="dv-storage-selected-text" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
          </div>
        </div>

        <!-- 3. Web URL Pane -->
        <div id="dv-pane-url" style="display:none;">
          <label class="dv-field">
            <span class="dv-field-label">चित्र का URL · Image URL *</span>
            <input type="url" name="imgurl" placeholder="https://example.com/photo.jpg" autocomplete="off"/>
          </label>
        </div>

        <!-- Alt & Caption Meta Fields -->
        <div style="margin-top:14px;border-top:1px solid #f5f5f4;padding-top:10px;">
          <label class="dv-field">
            <span class="dv-field-label">वैकल्पिक विवरण · Alt Text</span>
            <input type="text" name="alt" placeholder="चित्र का संक्षिप्त विवरण..." autocomplete="off"/>
          </label>
          <label class="dv-field" style="margin-top:8px;">
            <span class="dv-field-label">कैप्शन · Caption</span>
            <input type="text" name="caption" placeholder="लेख में चित्र के नीचे दिखने वाला कैप्शन..." autocomplete="off"/>
          </label>
        </div>
      `,
      onMount (dlg) {
        const tabUpload = dlg.querySelector('#dv-tab-upload-btn')
        const tabStorage = dlg.querySelector('#dv-tab-storage-btn')
        const tabUrl = dlg.querySelector('#dv-tab-url-btn')
        const paneUpload = dlg.querySelector('#dv-pane-upload')
        const paneStorage = dlg.querySelector('#dv-pane-storage')
        const paneUrl = dlg.querySelector('#dv-pane-url')

        const dropzone = dlg.querySelector('#dv-modal-dropzone')
        const fileInput = dlg.querySelector('#dv-modal-file-input')
        const changeInput = dlg.querySelector('#dv-file-change-input')
        const previewCard = dlg.querySelector('#dv-file-preview-card')
        const thumbImg = dlg.querySelector('#dv-file-thumb')
        const nameText = dlg.querySelector('#dv-file-name-text')
        const sizeText = dlg.querySelector('#dv-file-size-text')

        const storageLoading = dlg.querySelector('#dv-storage-loading')
        const storageEmpty = dlg.querySelector('#dv-storage-empty')
        const storageGrid = dlg.querySelector('#dv-storage-grid')
        const storageRefreshBtn = dlg.querySelector('#dv-storage-refresh-btn')
        const storageSelectedInfo = dlg.querySelector('#dv-storage-selected-info')
        const storageSelectedText = dlg.querySelector('#dv-storage-selected-text')

        const altInput = dlg.querySelector('[name="alt"]')

        function setTab (active) {
          mode = active
          const tabs = [
            { btn: tabUpload, pane: paneUpload, key: 'upload' },
            { btn: tabStorage, pane: paneStorage, key: 'storage' },
            { btn: tabUrl, pane: paneUrl, key: 'url' },
          ]
          tabs.forEach(t => {
            const isTarget = t.key === active
            t.btn.style.background = isTarget ? '#991b1b' : '#fff'
            t.btn.style.color = isTarget ? '#fff' : '#44403c'
            t.btn.style.border = isTarget ? 'none' : '1px solid #d6d3d1'
            t.pane.style.display = isTarget ? 'block' : 'none'
          })
          if (active === 'storage') {
            loadStorageImages()
          }
        }

        tabUpload.onclick = () => setTab('upload')
        tabStorage.onclick = () => setTab('storage')
        tabUrl.onclick = () => setTab('url')

        // File inputs trigger automatically via native label clicking
        fileInput.onchange = () => {
          if (fileInput.files && fileInput.files[0]) {
            handleFileSelection(fileInput.files[0])
          }
        }
        if (changeInput) {
          changeInput.onchange = () => {
            if (changeInput.files && changeInput.files[0]) {
              handleFileSelection(changeInput.files[0])
            }
          }
        }
        dropzone.onclick = e => {
          if (!e.target.closest('label')) {
            fileInput.click()
          }
        }

        function handleFileSelection (file) {
          if (!file) return
          if (!file.type.startsWith('image/')) {
            alert('केवल चित्र फ़ाइलें (JPEG, PNG, WebP, GIF) चुनी जा सकती हैं।')
            return
          }
          selectedFile = file
          const sizeKb = Math.round(file.size / 1024)
          nameText.textContent = file.name
          sizeText.textContent = sizeKb >= 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : sizeKb + ' KB'
          previewCard.style.display = 'flex'

          const reader = new FileReader()
          reader.onload = ev => {
            thumbImg.src = ev.target.result
          }
          reader.readAsDataURL(file)

          if (altInput && !altInput.value.trim()) {
            altInput.value = file.name.replace(/\.[^.]+$/, '')
          }
        }

        fileInput.onchange = () => {
          if (fileInput.files && fileInput.files[0]) {
            handleFileSelection(fileInput.files[0])
          }
        }

        // Drag and drop in dropzone
        dropzone.ondragover = e => {
          e.preventDefault()
          dropzone.style.borderColor = '#991b1b'
          dropzone.style.background = '#fef2f2'
        }
        dropzone.ondragleave = () => {
          dropzone.style.borderColor = '#d6d3d1'
          dropzone.style.background = '#fafaf9'
        }
        dropzone.ondrop = e => {
          e.preventDefault()
          dropzone.style.borderColor = '#d6d3d1'
          dropzone.style.background = '#fafaf9'
          const files = e.dataTransfer?.files
          if (files && files[0]) {
            handleFileSelection(files[0])
          }
        }

        // Storage Library Loader
        let storageLoaded = false
        async function loadStorageImages () {
          storageLoading.style.display = 'block'
          storageGrid.style.display = 'none'
          storageEmpty.style.display = 'none'

          try {
            const listUrl = uploadOpts.listUrl || '/admin/upload/images'
            const res = await fetch(listUrl, {
              headers: { 'Accept': 'application/json' },
            })
            if (!res.ok) throw new Error('स्टोरेज लोड नहीं हो सका।')
            const data = await res.json()
            const items = data.images || []
            storageLoading.style.display = 'none'

            if (!items.length) {
              storageEmpty.style.display = 'block'
              return
            }

            storageGrid.innerHTML = items.map(img => `
              <div class="dv-storage-card" data-url="${img.url}" data-name="${img.filename}" title="${img.filename} (${img.formatted_size})" style="position:relative;aspect-ratio:1;border-radius:6px;overflow:hidden;border:2px solid transparent;cursor:pointer;background:#e7e5e4;transition:all .15s ease;">
                <img src="${img.url}" alt="${img.filename}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;pointer-events:none;"/>
                <div class="dv-storage-chk" style="display:none;position:absolute;top:4px;right:4px;background:#991b1b;color:#fff;border-radius:50%;width:18px;height:18px;align-items:center;justify-content:center;font-size:11px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,0.3);">
                  ✓
                </div>
              </div>
            `).join('')

            storageGrid.style.display = 'grid'

            storageGrid.querySelectorAll('.dv-storage-card').forEach(card => {
              card.onclick = () => {
                storageGrid.querySelectorAll('.dv-storage-card').forEach(c => {
                  c.style.borderColor = 'transparent'
                  c.style.boxShadow = 'none'
                  c.querySelector('.dv-storage-chk').style.display = 'none'
                })
                card.style.borderColor = '#991b1b'
                card.style.boxShadow = '0 0 0 2px rgba(153,27,27,0.3)'
                card.querySelector('.dv-storage-chk').style.display = 'flex'

                selectedStorageUrl = card.getAttribute('data-url')
                const cardName = card.getAttribute('data-name')
                storageSelectedText.textContent = cardName
                storageSelectedInfo.style.display = 'flex'

                if (altInput && !altInput.value.trim()) {
                  altInput.value = cardName.replace(/\.[^.]+$/, '')
                }
              }
            })

            storageLoaded = true
          } catch (err) {
            storageLoading.textContent = 'त्रुटि: ' + (err?.message || 'लोड विफल')
          }
        }

        if (storageRefreshBtn) {
          storageRefreshBtn.onclick = () => loadStorageImages()
        }
      },
      onSubmit (dlg) {
        alt = dlg.querySelector('[name="alt"]').value.trim()
        caption = dlg.querySelector('[name="caption"]').value.trim()

        if (mode === 'upload') {
          if (!selectedFile) {
            alert('कृपया पहले कोई चित्र फ़ाइल चुनें। · Please choose an image file first.')
            return false
          }
        } else if (mode === 'storage') {
          if (!selectedStorageUrl) {
            alert('कृपया स्टोरेज गैलरी से कोई चित्र चुनें। · Please choose an image from storage gallery.')
            return false
          }
        } else if (mode === 'url') {
          url = dlg.querySelector('[name="imgurl"]').value.trim()
          if (!url) {
            alert('कृपया चित्र का URL दर्ज करें। · Please enter image URL.')
            return false
          }
        }
      },
    })

    if (!ok) return null

    if (mode === 'upload' && selectedFile) {
      try {
        const uploadedUrl = await uploadImage(selectedFile, uploadOpts)
        return { url: uploadedUrl, alt: alt || selectedFile.name.replace(/\.[^.]+$/, ''), caption }
      } catch (err) {
        alert(err?.message ?? 'अपलोड विफल रहा।')
        return null
      }
    }

    if (mode === 'storage' && selectedStorageUrl) {
      return { url: selectedStorageUrl, alt: alt || 'चित्र', caption }
    }

    if (mode === 'url' && url) {
      return { url, alt, caption }
    }

    return null
  }

  function promptTableSize (anchorEl) {
    return new Promise(resolve => {
      const ROWS = 8, COLS = 8

      const picker = document.createElement('div')
      picker.className = 'dv-tpicker'

      const grid = document.createElement('div')
      grid.className = 'dv-tpicker-grid'

      const label = document.createElement('div')
      label.className = 'dv-tpicker-label'
      label.textContent = '0 × 0'

      const cells = []
      for (let r = 1; r <= ROWS; r++) {
        for (let c = 1; c <= COLS; c++) {
          const cell = document.createElement('div')
          cell.className = 'dv-tpicker-cell'
          cell.dataset.r = r; cell.dataset.c = c
          cell.addEventListener('mouseover', () => highlight(r, c))
          cell.addEventListener('click', () => close({ rows: r, cols: c }))
          grid.appendChild(cell)
          cells.push(cell)
        }
      }

      picker.appendChild(grid)
      picker.appendChild(label)

      const rect = anchorEl.getBoundingClientRect()
      picker.style.top = `${rect.bottom + window.scrollY + 4}px`
      picker.style.left = `${rect.left + window.scrollX}px`
      document.body.appendChild(picker)

      function highlight (r, c) {
        cells.forEach(cell => cell.classList.toggle('hl', +cell.dataset.r <= r && +cell.dataset.c <= c))
        label.textContent = `${r} पंक्तियाँ × ${c} स्तंभ (${r} × ${c})`
      }

      function close (result) {
        picker.remove()
        document.removeEventListener('mousedown', outside)
        resolve(result)
      }

      function outside (e) { if (!picker.contains(e.target)) close(null) }
      setTimeout(() => document.addEventListener('mousedown', outside), 0)
    })
  }

  // ─────────────────────────────────────────────────────────────────
  //  Image Upload Handler
  // ─────────────────────────────────────────────────────────────────

  const MAX_BYTES = 10 * 1024 * 1024 // 10MB limit

  async function uploadImage (file, { url, csrfToken }) {
    if (!file.type.startsWith('image/')) throw new Error('केवल चित्र (JPEG, PNG, WebP) अपलोड किए जा सकते हैं।')
    if (file.size > MAX_BYTES) throw new Error('चित्र 10MB से अधिक नहीं होना चाहिए।')
    const fd = new FormData()
    fd.append('upload', file)
    fd.append('_token', csrfToken)
    const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json().catch(() => null)
    if (!res.ok || !data?.url) throw new Error(data?.error ?? 'अपलोड विफल रहा।')
    return data.url
  }

  function imgFilesFrom (dt) {
    return [...(dt?.files ?? [])].filter(f => f.type.startsWith('image/'))
  }

  function imgFilesFromPaste (e) {
    const fromFiles = imgFilesFrom(e.clipboardData)
    if (fromFiles.length) return fromFiles
    const blobs = []
    for (const item of (e.clipboardData?.items ?? [])) {
      if (item.kind === 'file' && item.type.startsWith('image/')) {
        const f = item.getAsFile(); if (f) blobs.push(f)
      }
    }
    return blobs
  }

  // ─────────────────────────────────────────────────────────────────
  //  Toolbar Button Definitions
  // ─────────────────────────────────────────────────────────────────

  const FULL_GROUPS = [
    // History
    { items: [
      { icon: 'undo', title: 'पूर्ववत करें · Undo (Ctrl+Z)', run: a => a._cmd('undo') },
      { icon: 'redo', title: 'फिर से करें · Redo (Ctrl+Y)', run: a => a._cmd('redo') },
    ]},
    // Block Styles
    { items: [
      { icon: 'format_paragraph', title: 'अनुच्छेद · Paragraph', run: a => a._block('P'), isActive: a => a._st.block === 'P' },
      { icon: 'title', title: 'मुख्य शीर्षक · Heading 2', run: a => a._block('H2'), isActive: a => a._st.block === 'H2' },
      { icon: 'format_size', title: 'उप-शीर्षक · Heading 3', run: a => a._block('H3'), isActive: a => a._st.block === 'H3' },
      { icon: 'format_quote', title: 'उद्धरण · Blockquote', run: a => a._block('BLOCKQUOTE'), isActive: a => a._st.inQuote },
    ]},
    // Inline Formatting
    { items: [
      { icon: 'format_bold', title: 'गाढ़ा · Bold (Ctrl+B)', run: a => a._cmd('bold'), isActive: a => a._st.bold },
      { icon: 'format_italic', title: 'तिरछा · Italic (Ctrl+I)', run: a => a._cmd('italic'), isActive: a => a._st.italic },
      { icon: 'format_underlined', title: 'रेखांकित · Underline (Ctrl+U)', run: a => a._cmd('underline'), isActive: a => a._st.underline },
      { icon: 'format_strikethrough', title: 'मध्यरेखा · Strikethrough', run: a => a._cmd('strikeThrough'), isActive: a => a._st.strike },
      { icon: 'format_ink_highlighter', title: 'हाइलाइट · Highlight', run: a => { toggleHighlight(a._surface); a._sync() }, isActive: a => a._st.highlight },
      { icon: 'superscript', title: 'ऊपर अंक · Superscript', run: a => a._cmd('superscript'), isActive: a => a._st.superscript },
      { icon: 'subscript', title: 'नीचे अंक · Subscript', run: a => a._cmd('subscript'), isActive: a => a._st.subscript },
      { icon: 'code', title: 'कोड ब्लॉक · Code Block', run: a => a._block('PRE'), isActive: a => a._st.code },
      { icon: 'format_clear', title: 'प्रारूपण हटाएं · Clear Formatting', run: a => { clearFormatting(a._surface); a._sync() } },
    ]},
    // Lists & Divider
    { items: [
      { icon: 'format_list_bulleted', title: 'बिंदु सूची · Bullet List', run: a => a._cmd('insertUnorderedList'), isActive: a => a._st.inList && a._st.listType === 'ul' },
      { icon: 'format_list_numbered', title: 'क्रमांकित सूची · Numbered List', run: a => a._cmd('insertOrderedList'), isActive: a => a._st.inList && a._st.listType === 'ol' },
      { icon: 'horizontal_rule', title: 'विभाजक रेखा · Divider', run: a => a._cmd('insertHorizontalRule') },
    ]},
    // Inserts (Media, Link, Table)
    { items: [
      { icon: 'add_photo_alternate', title: 'चित्र अपलोड · Upload Image', isFileUpload: true, run: a => a._pickImage() },
      { icon: 'image', title: 'चित्र जोड़ें / अपलोड · Insert / Upload Image', run: async a => {
          const res = await promptImageDialog({ url: a._uploadUrl, listUrl: '/admin/upload/images', csrfToken: a._csrfToken })
          if (res) {
            const fig = insertFigure(res.url, res.alt, res.caption, a._surface)
            a._sync()
            if (fig && a._selectFigure) a._selectFigure(fig)
          }
        },
      },
      { icon: 'link', title: 'लिंक · Link (Ctrl+K)', run: async a => {
          const res = await promptLink(a._st.link ?? '', a._st.linkTarget ?? '')
          if (res !== null) { applyLink(res.href, res.target, a._surface); a._sync() }
        }, isActive: a => !!a._st.link,
      },
      { icon: 'link_off', title: 'लिंक हटाएं · Remove Link', run: a => { applyLink('', '', a._surface); a._sync() } },
      { icon: 'table_chart', title: 'सारणी जोड़ें · Insert Table', run: async (a, btn) => {
          const s = await promptTableSize(btn)
          if (s) { insertBlock(buildTable(s.rows, s.cols), a._surface); a._sync() }
        }, isActive: a => a._st.inTable,
      },
    ]},
    // Alignment
    { items: [
      { icon: 'format_align_left', title: 'बायाँ संरेखण · Align Left', run: a => { setAlign('left', a._surface, a._selectedFigure?.()); a._sync() }, isActive: a => a._st.align === 'left' || a._st.align === 'start' },
      { icon: 'format_align_center', title: 'केंद्र संरेखण · Center', run: a => { setAlign('center', a._surface, a._selectedFigure?.()); a._sync() }, isActive: a => a._st.align === 'center' },
      { icon: 'format_align_right', title: 'दायाँ संरेखण · Align Right', run: a => { setAlign('right', a._surface, a._selectedFigure?.()); a._sync() }, isActive: a => a._st.align === 'right' },
      { icon: 'format_align_justify', title: 'समान संरेखण · Justify', run: a => { setAlign('justify', a._surface, a._selectedFigure?.()); a._sync() }, isActive: a => a._st.align === 'justify' },
    ]},
    // View modes
    { items: [
      { icon: 'code_blocks', title: 'HTML स्रोत · View HTML Source', run: a => a._toggleHtmlView() },
      { icon: 'fullscreen', title: 'पूर्ण स्क्रीन · Fullscreen (Esc)', run: a => a._toggleFullscreen() },
    ]},
  ]

  const LITE_GROUPS = [
    { items: [
      { icon: 'undo', title: 'Undo (Ctrl+Z)', run: a => a._cmd('undo') },
      { icon: 'redo', title: 'Redo (Ctrl+Y)', run: a => a._cmd('redo') },
    ]},
    { items: [
      { icon: 'format_bold', title: 'Bold (Ctrl+B)', run: a => a._cmd('bold'), isActive: a => a._st.bold },
      { icon: 'format_italic', title: 'Italic (Ctrl+I)', run: a => a._cmd('italic'), isActive: a => a._st.italic },
      { icon: 'format_underlined', title: 'Underline (Ctrl+U)', run: a => a._cmd('underline'), isActive: a => a._st.underline },
      { icon: 'format_strikethrough', title: 'Strikethrough', run: a => a._cmd('strikeThrough'), isActive: a => a._st.strike },
      { icon: 'format_clear', title: 'Clear formatting', run: a => { clearFormatting(a._surface); a._sync() } },
    ]},
    { items: [
      { icon: 'link', title: 'Link (Ctrl+K)', run: async a => {
          const res = await promptLink(a._st.link ?? '', a._st.linkTarget ?? '')
          if (res !== null) { applyLink(res.href, res.target, a._surface); a._sync() }
        }, isActive: a => !!a._st.link,
      },
      { icon: 'link_off', title: 'Remove link', run: a => { applyLink('', '', a._surface); a._sync() } },
    ]},
    { items: [
      { icon: 'format_list_bulleted', title: 'Bullet List', run: a => a._cmd('insertUnorderedList'), isActive: a => a._st.inList },
    ]},
  ]

  const TABLE_ITEMS = [
    { icon: 'table_rows', title: 'नीचे पंक्ति जोड़ें · Add Row Below', run: a => { modifyTable('row', 'add', a._surface); a._sync() } },
    { icon: 'add', title: 'दाएँ स्तंभ जोड़ें · Add Column Right', run: a => { modifyTable('column', 'add', a._surface); a._sync() } },
    null,
    { icon: 'remove', title: 'पंक्ति हटाएं · Delete Row', run: a => { modifyTable('row', 'delete', a._surface); a._sync() } },
    { icon: 'view_week', title: 'स्तंभ हटाएं · Delete Column', run: a => { modifyTable('column', 'delete', a._surface); a._sync() } },
    null,
    { icon: 'delete_forever', title: 'पूरी सारणी हटाएं · Delete Entire Table', run: a => { modifyTable('table', 'delete', a._surface); a._sync() } },
  ]

  // ─────────────────────────────────────────────────────────────────
  //  Editor Factory
  // ─────────────────────────────────────────────────────────────────

  function create (textarea, opts, groups, lite) {
    injectCss()

    const {
      uploadUrl   = '',
      csrfToken   = '',
      language    = 'hi',
      placeholder = 'लिखना शुरू करें…',
    } = opts

    // ── Elements ────────────────────────────────────────────────────
    const shell = document.createElement('div')
    shell.className = 'dv-ed'

    const toolbar = document.createElement('div')
    toolbar.className = 'dv-toolbar'
    toolbar.setAttribute('role', 'toolbar')
    toolbar.setAttribute('aria-label', 'Formatting toolbar')

    const tablebar = document.createElement('div')
    tablebar.className = 'dv-tablebar'
    tablebar.hidden = true

    const surface = document.createElement('div')
    surface.className = lite ? 'dv-surface lite' : 'dv-surface'
    surface.contentEditable = 'true'
    surface.setAttribute('role', 'textbox')
    surface.setAttribute('aria-multiline', 'true')
    surface.setAttribute('spellcheck', 'true')
    surface.setAttribute('data-placeholder', placeholder)
    surface.lang = language === 'en' ? 'en' : 'hi'

    const htmlView = document.createElement('textarea')
    htmlView.className = 'dv-html-view'
    htmlView.hidden = true

    const statusEl = document.createElement('div')
    statusEl.className = 'dv-status'
    statusEl.setAttribute('role', 'status')
    statusEl.setAttribute('aria-live', 'polite')
    statusEl.hidden = true

    shell.append(toolbar, tablebar, surface, htmlView, statusEl)
    textarea.parentNode.insertBefore(shell, textarea)
    textarea.hidden = true

    // ── Floating Image Controls Bubble ──────────────────────────────
    const imgBubble = document.createElement('div')
    imgBubble.className = 'dv-image-bubble'
    imgBubble.innerHTML = `
      <button type="button" class="dv-bubble-btn" data-align="left" title="बायाँ संरेखण (टेक्स्ट रैप) · Float Left with wrap">
        <span class="material-symbols-outlined">format_align_left</span>
        <span>बायाँ</span>
      </button>
      <button type="button" class="dv-bubble-btn" data-align="center" title="केंद्र संरेखण · Center">
        <span class="material-symbols-outlined">format_align_center</span>
        <span>केंद्र</span>
      </button>
      <button type="button" class="dv-bubble-btn" data-align="right" title="दायाँ संरेखण (टेक्स्ट रैप) · Float Right with wrap">
        <span class="material-symbols-outlined">format_align_right</span>
        <span>दायाँ</span>
      </button>
      <button type="button" class="dv-bubble-btn" data-align="full" title="पूर्ण चौड़ाई · Full width">
        <span class="material-symbols-outlined">width_full</span>
        <span>पूर्ण</span>
      </button>
      <span class="dv-bubble-sep"></span>
      <button type="button" class="dv-bubble-btn" data-act="meta" title="शीर्षक व विवरण बदलें · Edit Alt & Caption">
        <span class="material-symbols-outlined">edit</span>
        <span>Alt/कैप्शन</span>
      </button>
      <button type="button" class="dv-bubble-btn dv-bubble-btn-danger" data-act="delete" title="चित्र हटाएँ · Delete image">
        <span class="material-symbols-outlined">delete</span>
      </button>
    `
    shell.appendChild(imgBubble)

    let selectedFigure = null

    function deselectFigure () {
      if (selectedFigure) {
        selectedFigure.classList.remove('dv-figure-selected')
        selectedFigure = null
      }
      imgBubble.style.display = 'none'
    }

    function updateBubblePosition () {
      if (!selectedFigure || !selectedFigure.isConnected) {
        deselectFigure()
        return
      }
      const shellRect = shell.getBoundingClientRect()
      const figRect = selectedFigure.getBoundingClientRect()

      let top = figRect.top - shellRect.top + 8
      let left = figRect.left - shellRect.left + (figRect.width / 2)

      const minLeft = 140
      const maxLeft = Math.max(minLeft, shellRect.width - 140)
      left = Math.max(minLeft, Math.min(maxLeft, left))

      imgBubble.style.top = top + 'px'
      imgBubble.style.left = left + 'px'
      imgBubble.style.transform = 'translateX(-50%)'
      imgBubble.style.display = 'flex'

      const currentAlign = getFigureAlign(selectedFigure)
      imgBubble.querySelectorAll('[data-align]').forEach(btn => {
        btn.classList.toggle('is-active', btn.getAttribute('data-align') === currentAlign)
      })
    }

    function selectFigure (fig) {
      if (!fig) return
      if (selectedFigure && selectedFigure !== fig) {
        selectedFigure.classList.remove('dv-figure-selected')
      }
      selectedFigure = fig
      fig.classList.add('dv-figure-selected')
      updateBubblePosition()
      syncToolbar()
    }

    imgBubble.addEventListener('click', async e => {
      const btn = e.target.closest('button')
      if (!btn || !selectedFigure) return
      e.preventDefault()
      e.stopPropagation()

      const align = btn.getAttribute('data-align')
      const act = btn.getAttribute('data-act')

      if (align) {
        setFigureAlign(selectedFigure, align)
        updateBubblePosition()
        syncAll()
        syncToolbar()
      } else if (act === 'meta') {
        const img = selectedFigure.querySelector('img')
        const cap = selectedFigure.querySelector('figcaption')
        if (img) {
          const answer = await promptImageMeta(img.getAttribute('alt') ?? '', cap?.textContent?.trim() ?? '')
          if (answer) {
            img.setAttribute('alt', answer.alt)
            if (cap) {
              cap.textContent = answer.caption
              syncCaptionPlaceholders()
            }
            syncAll()
            updateBubblePosition()
          }
        }
      } else if (act === 'delete') {
        const fig = selectedFigure
        deselectFigure()
        fig.remove()
        syncAll()
        syncToolbar()
      }
    })

    // ── Status Toast ────────────────────────────────────────────────
    let stTimer = null
    function setStatus (msg, tone = 'info') {
      clearTimeout(stTimer)
      statusEl.textContent = msg
      statusEl.dataset.tone = tone
      statusEl.hidden = !msg
      if (msg && tone !== 'error') stTimer = setTimeout(() => { statusEl.hidden = true }, 4000)
    }

    // ── Word Count & Reading Time ────────────────────────────────────
    const countEl = document.createElement('span')
    countEl.className = 'dv-count'
    toolbar.appendChild(countEl)

    function syncCount () {
      const text = surface.innerText.replace(/\u200B/g, '').trim()
      const words = text ? text.split(/\s+/).filter(Boolean).length : 0
      const chars = text.length
      const wLabel = language === 'en' ? 'words' : 'शब्द'
      const cLabel = language === 'en' ? 'chars' : 'अक्षर'
      const mins = Math.max(1, Math.ceil(words / 180))
      const readLabel = language === 'en' ? `~${mins} min read` : `~${mins} मिनट पढ़ने का समय`

      countEl.innerHTML = `<span>${words} ${wLabel} · ${chars} ${cLabel}</span> <span class="dv-reading-time">${readLabel}</span>`
    }

    // ── Buttons Setup ───────────────────────────────────────────────
    let api // Forward declaration for closure reference in button click handlers
    const allBtns = []

    groups.forEach((group, gi) => {
      if (gi > 0) toolbar.insertBefore(sep(), countEl)
      group.items.forEach(spec => {
        if (spec.isFileUpload) {
          const label = document.createElement('label')
          label.className = 'dv-btn'
          label.title = spec.title
          label.setAttribute('aria-label', spec.title)
          label.setAttribute('data-tip', spec.title)
          label.style.cursor = 'pointer'
          label.style.position = 'relative'
          label.style.display = 'inline-flex'
          label.style.alignItems = 'center'
          label.style.justifyContent = 'center'

          const input = document.createElement('input')
          input.type = 'file'
          input.accept = 'image/*'
          input.multiple = true
          input.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;z-index:2;font-size:0;'

          input.addEventListener('change', () => {
            const files = [...(input.files ?? [])]
            input.value = ''
            files.forEach(addImage)
          })

          label.appendChild(icon(spec.icon))
          label.appendChild(input)
          label._spec = spec
          allBtns.push(label)
          toolbar.insertBefore(label, countEl)
          return
        }

        const btn = document.createElement('button')
        btn.type = 'button'
        btn.className = 'dv-btn'
        btn.title = spec.title
        btn.setAttribute('aria-label', spec.title)
        btn.setAttribute('aria-pressed', 'false')
        btn.setAttribute('data-tip', spec.title)
        btn.appendChild(icon(spec.icon))
        btn.addEventListener('mousedown', e => e.preventDefault())
        btn.addEventListener('click', () => {
          spec.run(api, btn)
          syncToolbar()
        })
        btn._spec = spec
        allBtns.push(btn)
        toolbar.insertBefore(btn, countEl)
      })
    })

    // ── Tablebar Buttons ────────────────────────────────────────────
    TABLE_ITEMS.forEach(spec => {
      if (!spec) { tablebar.appendChild(sep()); return }
      const btn = document.createElement('button')
      btn.type = 'button'
      btn.className = 'dv-btn'
      btn.title = spec.title
      btn.setAttribute('aria-label', spec.title)
      btn.setAttribute('data-tip', spec.title)
      btn.appendChild(icon(spec.icon))
      btn.addEventListener('mousedown', e => e.preventDefault())
      btn.addEventListener('click', () => { spec.run(api); syncToolbar() })
      tablebar.appendChild(btn)
    })

    // ── Toolbar State Synchronization ───────────────────────────────
    function syncToolbar () {
      if (!htmlView.hidden) return // Don't sync formatting states when in HTML view
      const st = api._st
      allBtns.forEach(btn => {
        const active = btn._spec.isActive ? Boolean(btn._spec.isActive(api)) : false
        btn.classList.toggle('is-active', active)
        btn.setAttribute('aria-pressed', active ? 'true' : 'false')
      })
      tablebar.hidden = !st.inTable
    }

    // ── Placeholder Handling ────────────────────────────────────────
    function syncPlaceholder () {
      const text = surface.innerText.replace(/\u200B/g, '').trim()
      const hasMedia = !!surface.querySelector('img,table,figure,hr')
      surface.classList.toggle('is-empty', !text && !hasMedia)
    }

    function syncCaptionPlaceholders () {
      surface.querySelectorAll('figcaption').forEach(c => {
        const empty = !c.textContent.trim()
        c.classList.toggle('is-empty', empty)
        if (empty) c.setAttribute('data-placeholder', 'चित्र का कैप्शन…')
        else c.removeAttribute('data-placeholder')
      })
    }

    // ── Image Upload Handling ───────────────────────────────────────
    async function addImage (file) {
      const skeleton = document.createElement('span')
      skeleton.className = 'dv-img-uploading'
      skeleton.contentEditable = 'false'
      insertBlock(skeleton, surface)
      setStatus('चित्र अपलोड हो रहा है… · Uploading image…')

      try {
        const url = await uploadImage(file, { url: uploadUrl, csrfToken })
        skeleton.remove()
        const fig = insertFigure(url, file.name.replace(/\.[^.]+$/, ''), '', surface)
        syncAll()
        if (fig) selectFigure(fig)
        setStatus('चित्र सफलता से जोड़ा गया। · Image uploaded successfully.', 'success')
      } catch (err) {
        skeleton.remove()
        setStatus(err?.message ?? 'अपलोड विफल रहा।', 'error')
      }
    }

    // Drag and drop images
    surface.addEventListener('dragover', e => {
      if (e.dataTransfer?.types?.includes('Files')) {
        e.preventDefault()
        surface.classList.add('is-dropping')
      }
    })
    surface.addEventListener('dragleave', e => {
      if (!surface.contains(e.relatedTarget)) surface.classList.remove('is-dropping')
    })
    surface.addEventListener('drop', e => {
      const files = imgFilesFrom(e.dataTransfer)
      if (!files.length) return
      e.preventDefault()
      surface.classList.remove('is-dropping')
      const sel = window.getSelection()
      if (sel && e.clientX) {
        const r = document.caretRangeFromPoint?.(e.clientX, e.clientY)
        if (r) { sel.removeAllRanges(); sel.addRange(r) }
      }
      files.forEach(addImage)
    })

    // Paste handler: images or clean HTML
    surface.addEventListener('paste', e => {
      const files = imgFilesFromPaste(e)
      if (files.length) {
        e.preventDefault()
        files.forEach(addImage)
        return
      }

      const html = e.clipboardData?.getData('text/html')
      if (html) {
        e.preventDefault()
        const clean = cleanPastedHtml(html)
        document.execCommand('insertHTML', false, clean)
        syncAll()
      }
    })

    // Click image or figure to select and reveal alignment toolbar
    surface.addEventListener('click', e => {
      const img = e.target instanceof HTMLElement && e.target.tagName === 'IMG' ? e.target : null
      const fig = e.target instanceof HTMLElement ? e.target.closest('figure') : null

      if (img || fig) {
        let targetFig = fig || img?.closest?.('figure')
        if (img && !targetFig) {
          targetFig = document.createElement('figure')
          setFigureAlign(targetFig, 'center')
          img.replaceWith(targetFig)
          targetFig.appendChild(img)
          const cap = document.createElement('figcaption')
          cap.setAttribute('data-placeholder', 'चित्र का कैप्शन…')
          targetFig.appendChild(cap)
        }
        if (targetFig) {
          selectFigure(targetFig)
          return
        }
      }

      deselectFigure()
    })

    // Double-click image to edit Alt and Caption directly
    surface.addEventListener('dblclick', async e => {
      if (e.target instanceof HTMLElement && e.target.tagName === 'IMG') {
        e.preventDefault()
        const img = e.target
        const fig = img.closest('figure')
        const cap = fig?.querySelector('figcaption')
        const answer = await promptImageMeta(img.getAttribute('alt') ?? '', cap?.textContent?.trim() ?? '')
        if (answer) {
          img.setAttribute('alt', answer.alt)
          if (cap) {
            cap.textContent = answer.caption
            syncCaptionPlaceholders()
          }
          syncAll()
          if (fig) selectFigure(fig)
        }
      }
    })

    // Scroll and resize listeners to reposition the floating bubble
    surface.addEventListener('scroll', () => {
      if (selectedFigure) updateBubblePosition()
    })
    window.addEventListener('resize', () => {
      if (selectedFigure) updateBubblePosition()
    })

    // ── Synchronization to Textarea ─────────────────────────────────
    function syncAll () {
      surface.querySelectorAll('.dv-figure-selected').forEach(f => f.classList.remove('dv-figure-selected'))

      // Convert any leftover <span style="background-color: ..."> to <mark>
      surface.querySelectorAll('span').forEach(s => {
        if (s.style.backgroundColor && s.style.backgroundColor !== 'transparent') {
          const m = document.createElement('mark')
          while (s.firstChild) m.appendChild(s.firstChild)
          s.replaceWith(m)
        }
      })

      textarea.value = surface.innerHTML
      if (selectedFigure) selectedFigure.classList.add('dv-figure-selected')
      syncCaptionPlaceholders()
      syncPlaceholder()
    }

    surface.addEventListener('input', () => { syncAll(); syncCount(); syncToolbar() })
    surface.addEventListener('blur',  () => syncAll())
    surface.addEventListener('keyup',   () => syncToolbar())
    surface.addEventListener('mouseup', () => syncToolbar())

    document.addEventListener('selectionchange', () => {
      const active = document.activeElement
      if (active === surface || surface.contains(active)) syncToolbar()
    })

    // ── Keyboard Shortcuts ──────────────────────────────────────────
    surface.addEventListener('keydown', e => {
      // Ctrl/Cmd shortcuts
      if ((e.ctrlKey || e.metaKey) && !e.altKey) {
        const k = e.key.toLowerCase()
        if (k === 'b') { e.preventDefault(); execCmd('bold', null, surface); syncToolbar(); return }
        if (k === 'i') { e.preventDefault(); execCmd('italic', null, surface); syncToolbar(); return }
        if (k === 'u') { e.preventDefault(); execCmd('underline', null, surface); syncToolbar(); return }
        if (k === 'k') {
          e.preventDefault()
          promptLink(api._st.link ?? '', api._st.linkTarget ?? '').then(res => {
            if (res !== null) { applyLink(res.href, res.target, surface); syncAll() }
          })
          return
        }
        if (k === '\\') { e.preventDefault(); clearFormatting(surface); syncAll(); return }
      }

      // Inside <pre>: Enter inserts newline \n
      if (e.key === 'Enter') {
        const sel = window.getSelection()
        const pre = sel && sel.rangeCount ? closestEl(sel.anchorNode, surface)?.closest?.('pre') : null
        if (pre) {
          e.preventDefault()
          execCmd('insertText', '\n', surface)
          return
        }
      }

      // Inside Table: Tab navigates cells (Shift+Tab goes backward). Adds row if at end!
      if (e.key === 'Tab') {
        const sel = window.getSelection()
        const cell = sel && sel.rangeCount ? closestEl(sel.anchorNode, surface)?.closest?.('td,th') : null
        if (cell) {
          e.preventDefault()
          const table = cell.closest('table')
          const allCells = [...table.querySelectorAll('td,th')]
          const idx = allCells.indexOf(cell)

          if (!e.shiftKey && idx === allCells.length - 1) {
            // At last cell: add a new row and focus its first cell
            modifyTable('row', 'add', surface)
            const updatedCells = [...table.querySelectorAll('td,th')]
            const targetCell = updatedCells[idx + 1]
            if (targetCell) {
              const r = document.createRange()
              r.selectNodeContents(targetCell)
              r.collapse(false)
              sel.removeAllRanges()
              sel.addRange(r)
            }
            syncAll()
            return
          }

          const next = e.shiftKey ? allCells[idx - 1] : allCells[idx + 1]
          if (next) {
            const r = document.createRange()
            r.selectNodeContents(next)
            r.collapse(false)
            sel.removeAllRanges()
            sel.addRange(r)
          }
          return
        }
      }

      // Delete selected figure with Backspace or Delete key (when not typing in caption)
      if (selectedFigure && (e.key === 'Delete' || e.key === 'Backspace')) {
        const sel = window.getSelection()
        const isEditingCaption = sel && sel.anchorNode && closestEl(sel.anchorNode, surface)?.closest?.('figcaption')
        if (!isEditingCaption && (document.activeElement === surface || !document.activeElement?.closest('figcaption'))) {
          e.preventDefault()
          const fig = selectedFigure
          deselectFigure()
          fig.remove()
          syncAll()
          syncToolbar()
          return
        }
      }

      // Exit fullscreen on Escape
      if (e.key === 'Escape' && shell.classList.contains('is-fullscreen')) {
        api._toggleFullscreen()
      }
    })

    // ── Initial Value Load ──────────────────────────────────────────
    const initVal = textarea.value || ''
    if (lite && initVal && !initVal.includes('<')) {
      // Lite mode: convert newline-separated plain text to paragraphs
      surface.innerHTML = initVal.split('\n').filter(Boolean).map(l => `<p>${l}</p>`).join('')
    } else {
      surface.innerHTML = initVal
    }

    // ── Public API ──────────────────────────────────────────────────
    api = {
      _surface: surface,
      _uploadUrl: uploadUrl,
      _csrfToken: csrfToken,
      _selectFigure: selectFigure,
      _selectedFigure: () => selectedFigure,
      get _st () { return queryState(surface, selectedFigure) },
      _cmd   (cmd, val) { execCmd(cmd, val, surface); syncAll() },
      _block (tag)      { toggleBlock(tag, surface);  syncAll() },
      _sync  ()         { syncAll() },
      _pickImage ()     { const inp = toolbar.querySelector('input[type=file]'); if (inp) inp.click(); },

      _toggleHtmlView () {
        const isHtml = !htmlView.hidden
        if (isHtml) {
          // Switch back to WYSIWYG
          surface.innerHTML = htmlView.value
          htmlView.hidden = true
          surface.hidden = false
          syncAll()
          syncCount()
          syncToolbar()
          surface.focus()
        } else {
          // Switch to HTML view
          syncAll()
          htmlView.value = surface.innerHTML
          surface.hidden = true
          tablebar.hidden = true
          htmlView.hidden = false
          htmlView.focus()
          htmlView.oninput = () => { textarea.value = htmlView.value }
        }
      },

      _toggleFullscreen () {
        const isFull = shell.classList.toggle('is-fullscreen')
        document.body.style.overflow = isFull ? 'hidden' : ''
        const btn = allBtns.find(b => b._spec.icon === 'fullscreen' || b._spec.icon === 'fullscreen_exit')
        if (btn) {
          btn.querySelector('.material-symbols-outlined').textContent = isFull ? 'fullscreen_exit' : 'fullscreen'
          btn.setAttribute('data-tip', isFull ? 'फुलस्क्रीन बंद करें · Exit Fullscreen (Esc)' : 'पूर्ण स्क्रीन · Fullscreen (Esc)')
        }
        surface.focus()
      },

      focus () {
        if (!surface.hidden) surface.focus()
        else htmlView.focus()
      },

      destroy () {
        if (shell.classList.contains('is-fullscreen')) document.body.style.overflow = ''
        shell.remove()
        textarea.hidden = false
      },
    }

    syncCaptionPlaceholders()
    syncPlaceholder()
    syncCount()
    syncToolbar()

    return api
  }

  // ─────────────────────────────────────────────────────────────────
  //  Public Module Exports
  // ─────────────────────────────────────────────────────────────────

  return {
    create (textarea, options = {}) {
      return create(textarea, options, FULL_GROUPS, false)
    },
    createLite (textarea, options = {}) {
      return create(textarea, options, LITE_GROUPS, true)
    },
  }
})()
