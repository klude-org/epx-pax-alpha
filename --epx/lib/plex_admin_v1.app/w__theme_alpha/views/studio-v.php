      <!-- CONTENT (fills remaining viewport; only main scrolls) -->
    <main class="xui-page-content d-flex flex-column">
        <div class="d-flex flex-fill" id="xui-shell">
            <!-- ################################################################################################### -->
            <!-- Floating collapse tab -->
            <style>
                /* COLLAPSE TAB (90° rotated trapezoid) */
                .xui-sidebar-toggle {
                    position: fixed;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 15px;
                    height: 40px;
                    padding: 0;
                    border: none;
                    outline: none;
                    cursor: pointer;
                    background: rgba(180, 180, 180, 0.25);
                    backdrop-filter: blur(4px);
                    box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    clip-path: polygon(0 0, 100% 15%, 100% 85%, 0 100%);
                    color: rgba(0, 0, 0, 0.55);
                    transition: background .2s ease, color .2s ease;
                    z-index: 1040;
                }

                .xui-sidebar-toggle:hover {
                    background: rgba(108, 117, 125, .92);
                    color: #fff;
                }

                .xui-sidebar-toggle .bi {
                    display: inline-block;
                }

                /* Collapsed state */
                .xui-collapsed .xui-page-sidebar,
                .xui-collapsed .xui-drag-handle {
                    display: none !important;
                }

                .xui-collapsed .xui-page-main {
                    width: 100% !important;
                }
            </style>
            <button class="xui-sidebar-toggle btn btn-sm text-white border-0" id="xuiCollapseBtn" type="button" title="Toggle sidebar">
                <i class="bi bi-chevron-left" id="xuiCollapseIcon"></i>
            </button>
            <script>
                $(() => {
                    (function xui__sidebar_toggle() {
                        const btn = document.getElementById("xuiCollapseBtn");
                        const icon = document.getElementById("xuiCollapseIcon");
                        const root = document.body;

                        const restore = sessionStorage.getItem(XUI_SITE_STORE.xui__sidebar_toggle) === "1";
                        if (restore) root.classList.add("xui-collapsed");
                        icon.className = root.classList.contains("xui-collapsed") ? "bi bi-chevron-right" : "bi bi-chevron-left";

                        btn.addEventListener("click", () => {
                            root.classList.toggle("xui-collapsed");
                            const collapsed = root.classList.contains("xui-collapsed");
                            icon.className = collapsed ? "bi bi-chevron-right" : "bi bi-chevron-left";
                            sessionStorage.setItem(XUI_SITE_STORE.xui__sidebar_toggle, collapsed ? "1" : "0");
                        });
                    })();
                })
            </script>
            
            
            <!-- ################################################################################################### -->
            <!-- Sidebar -->
            <style>
                :root {
                    --xui-sidebar-w: 260px; /* default */
                }        
                
                /* ONLY main scrolls */
                .xui-page-sidebar {
                    min-width: 160px;
                    width: var(--xui-sidebar-w, 260px);
                    max-width: 60vw;
                    overflow: hidden;
                    /* background: #f8f9fa; */
                    border-right: 1px solid rgba(0, 0, 0, .1);
                }

                /* Sidebar inner scroll (thin left scrollbar) */
                /* LEFT scrollbar without layout shifts */
                .xui-sidebar-scroll {
                    height: 100%;
                    overflow-y: auto;

                    /* keep layout stable even when the scrollbar shows/hides */
                    scrollbar-gutter: stable;

                    /* reserve exact scrollbar width we detect via JS below */
                    padding-inline-start: var(--xui-sbw, 0px);

                    /* your existing cosmetics (optional) */
                    padding: .1rem;
                    scrollbar-width: thin;
                    /* Firefox */
                    /* scrollbar-color: #bbb transparent; */
                }

                /* Force sidebar scroll to appear on the left side */
                .xui-sidebar-scroll {
                    direction: rtl;
                    /* scrollbar on the left */
                    text-align: left;
                    /* restore left-aligned content */
                }

                .xui-sidebar-scroll>* {
                    /* restore normal content direction */
                    direction: ltr;
                }

                .xui-sidebar-scroll::-webkit-scrollbar {
                    width: 6px;
                }

                .xui-sidebar-scroll::-webkit-scrollbar-thumb {
                    /* background-color: #bbb; */
                    border-radius: 3px;
                }

                .xui-page-sidebar .search {
                    padding: .5rem .2rem;
                    /* border-bottom: 1px solid #4e5b63ff */
                }

                .xui-page-sidebar .packages {
                    overflow: auto;
                    /* padding: .75rem; */
                    gap: .75rem;
                    display: flex;
                    flex-direction: column
                }

                .xui-sidenav-item-card {
                    /* border: 1px solid #e3e6ea; */
                    /* background: #fff; */
                    border-radius: .75rem;
                    padding: .5rem .75rem;
                    cursor: pointer;
                    transition: border-color .15s, box-shadow .15s, background .15s
                }

                .xui-sidenav-item-card:hover {
                    border-color: #cfd4da;
                    box-shadow: 0 1px 8px rgba(0, 0, 0, .05)
                }

                .xui-sidenav-item-card.active {
                    border-color: #0d6efd;
                    box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .15)
                }
                

                [data-bs-theme="dark"] .xui-sidenav-item-card.active {
                    background-color: var(--bs-primary-bg) !important;
                    color: var(--bs-primary-text) !important;
                }
                
                .xui-sidenav-title {
                    font-weight: 600;
                    font-size: .95rem;
                    margin: 0;
                    /* word-break: break-all; */
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }

                .xui-sidenav-desc {
                    color: #6c757d;
                    font-size: .84rem;
                    margin: .25rem 0 0;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis
                }
                
                /* Drag handle */
                .xui-drag-handle {
                    width: 6px;
                    cursor: col-resize;
                    background: transparent;
                    border-right: 1px solid rgba(0, 0, 0, .1);
                }

                .xui-drag-handle:hover {
                    background: rgba(0, 0, 0, .05);
                }

                .xui-drag-shield {
                    position: fixed;
                    inset: 0;
                    cursor: col-resize;
                    z-index: 1040;
                    display: none;
                }
            </style>
            <!-- Resizer Prefill (!!!! MUST COME BEFORE SIDEBAR !!!!)-->
            <script>
                (function () {
                    try {
                        const w = sessionStorage.getItem(XUI_SITE_STORE.xui__sidebar_resizer);
                        if (w) {
                            document.documentElement.style.setProperty('--xui-sidebar-w', w + "px");
                        }
                    } catch (e) {
                        // localStorage might be blocked; just ignore
                    }
                })();
            </script>
            <!-- RESIZE LOGIC -->
            <script>
                $(() => {
                    (function xui__sidebar_resizer() {
                        const handle = document.getElementById("xuiDragHandle");
                        const shield = document.getElementById("xuiDragShield");
                        const sidebar = document.querySelector(".xui-page-sidebar");
                        if (!handle || !shield || !sidebar) return;

                        const savedW = sessionStorage.getItem(XUI_SITE_STORE.xui__sidebar_resizer);
                        if (savedW) document.documentElement.style.setProperty("--xui-sidebar-w", savedW + "px");

                        let startX = 0, startW = 0, dragging = false;

                        const startDrag = (ev) => {
                            dragging = true;
                            startX = (ev.touches ? ev.touches[0].clientX : ev.clientX);
                            startW = sidebar.getBoundingClientRect().width;
                            shield.style.display = "block";
                            document.body.classList.add("user-select-none");
                        };
                        const onDrag = (ev) => {
                            if (!dragging) return;
                            const clientX = (ev.touches ? ev.touches[0].clientX : ev.clientX);
                            let newW = startW + (clientX - startX);
                            newW = Math.max(160, Math.min(newW, window.innerWidth * 0.6));
                            document.documentElement.style.setProperty("--xui-sidebar-w", newW + "px");
                        };
                        const endDrag = () => {
                            if (!dragging) return;
                            dragging = false;
                            shield.style.display = "none";
                            document.body.classList.remove("user-select-none");
                            const finalW = sidebar.getBoundingClientRect().width | 0;
                            sessionStorage.setItem(XUI_SITE_STORE.xui__sidebar_resizer, String(finalW));
                        };

                        handle.addEventListener("mousedown", startDrag);
                        handle.addEventListener("touchstart", startDrag, { passive: true });
                        window.addEventListener("mousemove", onDrag);
                        window.addEventListener("touchmove", onDrag, { passive: false });
                        window.addEventListener("mouseup", endDrag);
                        window.addEventListener("touchend", endDrag);
                        window.addEventListener("touchcancel", endDrag);
                    })();
                })
            </script>
            <aside class="xui-page-sidebar d-flex flex-column">
                <?php if($ix = $this->vars['sidebar/header']): ?>
                    <div class="p-2 border-bottom d-flex align-items-center justify-content-between">
                        <?= $ix ?>
                    </div>
                <?php else: ?>
                    
                <?php endif ?>
                <div class="xui-sidebar-scroll flex-fill packages" id="xui-sidenav-list">
                    <?php if($ix = $this->vars['sidebar/middle']): ?>
                        <?= $ix ?>
                    <?php else: ?>
                    <?php endif ?>
                </div>
                <div class="flex-shrink p-2 d-flex">
                    <?php if($ix = $this->vars['sidebar/footer']): ?>
                        <?= $ix ?>
                    <?php else: ?>
                        <a class="btn btn-outline-primary w-100" href="?--logout">Logout</a>    
                    <?php endif ?>
                </div>
            </aside>
            <div class="xui-drag-handle" id="xuiDragHandle" title="Drag to resize"></div>
            <div class="xui-drag-shield" id="xuiDragShield"></div>
            <script>
                $(() => {
                    $('#filter').on('input', () => {
                        const term = $('#filter').val().toLowerCase();
                        $('.xui-sidenav-item-card').each((i, c) => {
                            c.style.display = c.textContent.toLowerCase().includes(term) ? '' : 'none';
                        });
                    });
                    $(document).on('keydown', function (e) {
                        if (e.ctrlKey && e.key === '/') {
                            e.preventDefault();
                            $('#filter').focus().select();
                        }
                    });
                })    
            </script>



            <!-- ################################################################################################### -->
            <!-- Main (only this scrolls) -->
            <style>
                /* important for Firefox/Chrome to let children overflow */
                .xui-page-main {
                    overflow: auto;
                    min-width: 0;
                }

                /* Tab bodies */
                .xui-page-tabbody {
                    display: none;
                }

                .xui-page-tabbody.active {
                    display: block;
                }

            </style>
            <main id="main" class="xui-page-main d-flex flex-fill">
                <?php if($ix = $this->vars['main']): ?>
                    <?= $ix ?>
                <?php endif ?>
            </main>
            <script>
                
            </script>
        </div>
    </main>