<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?=$this->vars['ui/page/tab/title'] ?? 'Untitled'?></title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

    <script>
        const X_CSRF = <?= json_encode($_SESSION['--CSRF']); ?>;
        if (typeof window.xui === 'undefined') {
            window.xui = {
                url: {
                    root: '<?=$_SERVER['root_url'] ?? ''?>',
                    site: '<?=$_SERVER['site_url'] ?? ''?>',
                    base: '<?=$_SERVER['base_url'] ?? ''?>',
                    ctlr: '<?=$_SERVER['ctlr_url'] ?? ''?>',
                },
                ds: {
                    //datasources  
                },
                i: [],
                init(f = null) {
                    if (f) {
                        this.i.push(f);
                    }
                    return this;
                },
                _init_() {
                    this.i.forEach(function (f) {
                        f();
                    });
                },
                extend(a1, a2 = null) {
                    if (typeof a1 === 'string') {
                        if (typeof this[a1] === 'undefined') {
                            this[a1] = {};
                        }
                        if (a2) {
                            Object.assign(this[a1], a2);
                        }
                    } else if (a1 instanceof Object) {
                        Object.assign(this, a1);
                    }
                    return this;
                },
                event: {
                    _list: [],
                    add(name, delegate) {
                        if (this._list[name]) {
                            this._list[name].push(delegate);
                        } else {
                            this._list[name] = [delegate];
                        }
                    },
                    trigger(name, data) {
                        this._list[name]?.forEach(delegate => {
                            delegate(data);
                        });
                    }
                },
            };
        }

        if (typeof xui.TRACE === 'undefined') {
            xui.TRACE = 6;
            xui.TRACE_1 = ((xui.TRACE ?? 0) >= 1);
            xui.TRACE_2 = ((xui.TRACE ?? 0) >= 2);
            xui.TRACE_3 = ((xui.TRACE ?? 0) >= 3);
            xui.TRACE_4 = ((xui.TRACE ?? 0) >= 4);
            xui.TRACE_5 = ((xui.TRACE ?? 0) >= 5);
            xui.TRACE_6 = ((xui.TRACE ?? 0) >= 6);
            xui.TRACE_7 = ((xui.TRACE ?? 0) >= 7);
            xui.TRACE_8 = ((xui.TRACE ?? 0) >= 8);
            xui.TRACE_9 = ((xui.TRACE ?? 0) >= 9);
            (xui.TRACE_1) && console.log({
                xui
            });
        }

        window.onload = (event) => {
            xui._init_();
        };

    </script>

    <style>
        /* RESET & HEIGHT CONTROL -------------------------------- */
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .xui-page-content {
            flex: 1 1 auto;
            min-height: 0;
        }

        /* allow internal overflow without body scroll */
        #xui-shell {
            min-height: 0;
        }

        /* Header styling */
        .xui-page-header {
            border-bottom: 1px solid rgba(0, 0, 0, .1);
            background: #fff;
        }
    </style>

    <style>
        .select2-container .select2-selection--single {
            height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }


        /* ==== Select2 fix when inside .input-group ==== */
        .input-group>.select2-container {
            flex: 1 1 auto !important;
            width: 1% !important;
            min-width: 0;
        }

        /* Ensure height + alignment match Bootstrap input size */
        .input-group-sm>.select2-container .select2-selection--single {
            height: calc(1.65rem + 2px) !important;
            line-height: 1.5rem !important;
            padding: 0 .375rem;
            border-color: var(--bs-border-color);
            border-radius: 0 .25rem .25rem 0;
        }

        /* Match border radius for left/right sides when adjacent to text addons */
        .input-group>.select2-container:first-child .select2-selection--single {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group>.select2-container:last-child .select2-selection--single {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* Keep select2 dropdown below other elements correctly */
        .select2-container {
            z-index: 1055;
            /* above .dropdown-menu (1000) */
        }

        /* Apply dark mode styles when Bootstrap is in dark theme */
        [data-bs-theme="dark"] .select2-container .select2-selection {
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            border-color: var(--bs-border-color) !important;
        }

        [data-bs-theme="dark"] .select2-container .select2-selection__rendered {
            color: var(--bs-body-color) !important;
        }

        [data-bs-theme="dark"] .select2-container .select2-selection__arrow b {
            border-color: var(--bs-body-color) transparent transparent transparent !important;
        }

        /* Dropdown menu */
        [data-bs-theme="dark"] .select2-dropdown {
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            border-color: var(--bs-border-color) !important;
        }

        [data-bs-theme="dark"] .select2-results__option--highlighted {
            background-color: var(--bs-primary-bg-subtle) !important;
            color: var(--bs-primary-text) !important;
        }

        [data-bs-theme="dark"] .select2-container--default .select2-results__option--selected {
            background-color: var(--bs-primary-bg) !important;
            color: var(--bs-primary-text-emphasis, var(--bs-body-color)) !important;
        }
    </style>


    <script>
        const XUI_CTLR_STORE = (() => {
            const baseKey = (location.origin + location.pathname).toLowerCase();
            return new Proxy({}, {
                get(_, prop) {
                    if (typeof prop === "string") {
                        // For convenience, allow dotted or camelCase keys: activeTab, sidebarWidth, etc.
                        return `${baseKey}::xui.${prop}`;
                    }
                    return undefined;
                }
            });
        })();
        const XUI_SITE_STORE = (() => {
            const baseKey = (window.xui.url.site).toLowerCase();
            return new Proxy({}, {
                get(_, prop) {
                    if (typeof prop === "string") {
                        // For convenience, allow dotted or camelCase keys: activeTab, sidebarWidth, etc.
                        return `${baseKey}::xui.${prop}`;
                    }
                    return undefined;
                }
            });
        })();        
    </script>

</head>

<body>

    <?= $this->inset() ?>

    <!-- ################################################################################################### -->
    <!-- Dark mode toggle -->
    <style>
        .theme-toggle-btn {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 2100;
        }

        .theme-toggle-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .35);
        }
    </style>

    <button type="button" id="theme-toggle" class="btn btn-primary btn-sm rounded-circle theme-toggle-btn" aria-label="Toggle dark mode">
        <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
    </button>

    <script>
        // Theme init (Bootstrap 5.3 data-bs-theme)
        (function () {
            const storedTheme = sessionStorage.getItem(XUI_SITE_STORE.xui_dark_mode);
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
        $('#theme-toggle').on('click', function () {
            var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            var next = current === 'light' ? 'dark' : 'light';
            // swap icon
            const icon = document.getElementById('themeToggleIcon');
            if (next === 'dark') {
                icon.classList.remove('bi-moon-stars');
                icon.classList.add('bi-sun');
            } else {
                icon.classList.remove('bi-sun');
                icon.classList.add('bi-moon-stars');
            }
            document.documentElement.setAttribute('data-bs-theme', next);
            sessionStorage.setItem(XUI_SITE_STORE.xui_dark_mode, next);
        });        
    </script>

    <!-- ################################################################################################### -->
    <!-- Toast -->
    <style>
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }

        .toast {
            display: flex;
            align-items: center;
            background-color: #333;
            color: #fff;
            padding: 12px 20px;
            margin-bottom: 10px;
        }

        .toast {
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: translateY(-20px);
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.hide {
            opacity: 0;
            transform: translateY(-20px);
        }
    </style>
    <div id="toast-container"></div>
    <script>
        <?php $toasts = \json_encode($this -> _['flash_in']['toasts'] ?? []); ?>
        $(() => {
            var toasts = <?= $toasts ?>;
            if (toasts) {
                // Make the toast container visible
                const toastContainer = document.getElementById('toast-container');
                toastContainer.style.display = 'block';

                toasts.forEach((message) => {
                    if (message) {
                        // Create a new toast element
                        const toast = document.createElement('div');
                        toast.classList.add('toast');
                        toast.textContent = message;

                        // Append to the toast container
                        toastContainer.appendChild(toast);

                        // Show the toast with animation
                        setTimeout(() => toast.classList.add('show'), 100);

                        // Hide the toast and container after 3 seconds
                        setTimeout(() => {
                            toast.classList.add('hide');
                            toast.addEventListener('transitionend', () => {
                                toast.remove();
                                if (toastContainer.children.length === 0) {
                                    toastContainer.style.display = 'none';
                                }
                            });
                        }, 3000);
                    }
                });
            }
        });
    </script>

    <!-- ################################################################################################### -->
    <!-- Page Spinner -->
    <style>
        /* Spinner */
        .xui-page-spinner-overlay {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .8);
            z-index: 2
        }

        .xui-page-spinner-border {
            width: 2.75rem;
            height: 2.75rem
        }
    </style>
    <div class="xui-page-spinner-overlay spinner-overlay" id="xui-page-spinner">
        <div class="xui-page-spinner-border spinner-border" role="status" aria-label="Loading…"></div>
    </div>
    <script>
        window.xui__page_spinner = {
            show() {
                $('#xui-page-spinner')[0].style.display = 'flex';
            },
            hide() {
                $('#xui-page-spinner')[0].style.display = 'none';
            },
        };
    </script>

    <!-- ################################################################################################### -->
    <!-- Generic Prompt Modal -->
    <div class="modal fade" id="xuiPromptModal" tabindex="-1" aria-hidden="true" aria-labelledby="xuiPromptTitle">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="xuiPromptTitle">Prompt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="xuiPromptMessage">
                    <!-- message gets injected here -->
                </div>
                <div class="modal-footer" id="xuiPromptButtons">
                    <!-- buttons get injected here -->
                </div>
            </div>
        </div>
    </div>
    <script>
        window.xui = window.xui || {};
        window.xui.modal = window.xui.modal || {};
        (function () {
            const modalEl = document.getElementById('xuiPromptModal');
            const titleEl = document.getElementById('xuiPromptTitle');
            const msgEl = document.getElementById('xuiPromptMessage');
            const buttonsEl = document.getElementById('xuiPromptButtons');

            const bsPromptModal = new bootstrap.Modal(modalEl, {
                backdrop: 'static', // click-outside doesn't auto-confirm
                keyboard: true
            });

            let currentCallback = null;
            let hasClickedButton = false;

            function finish(choice) {
                if (!currentCallback) return;
                const cb = currentCallback;
                currentCallback = null;
                cb(choice);
            }

            // When modal fully hides (Esc / close button / backdrop)
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (!hasClickedButton) {
                    // closed without choosing a button
                    finish(null);
                }
                hasClickedButton = false;
            });

            window.xui.modal.prompt = function (message, title, buttons, callback) {
                titleEl.textContent = title || 'Confirm';
                msgEl.textContent = message || '';

                // Default buttons if none supplied
                const btnLabels = (Array.isArray(buttons) && buttons.length) ? buttons : ['OK'];

                // Clear previous buttons
                buttonsEl.innerHTML = '';
                currentCallback = (typeof callback === 'function') ? callback : null;

                btnLabels.forEach((label, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    // First button primary, others outline-secondary by default
                    btn.className = 'btn ' + (idx === 0 ? 'btn-danger' : 'btn-outline-secondary');
                    btn.textContent = label;

                    btn.addEventListener('click', function () {
                        hasClickedButton = true;
                        bsPromptModal.hide();
                        finish(label); // pass the label to the callback, e.g. "Yes" / "No"
                    }, { once: true });

                    buttonsEl.appendChild(btn);
                });

                bsPromptModal.show();
            };
        })();
    </script>

    <script>
        $(function () {
            var FIELD = '--csrf';
            var token = $('meta[name="csrf-token"]').attr('content') || (window.__CSRF_TOKEN__ || '<?=$_SESSION['--CSRF'] ?? ''?>');
            $('form').each(function () {
                var $f = $(this), $h = $f.find('input[type="hidden"][name="' + FIELD + '"]');
                if ($h.length) $h.val(token);
                else $('<input>', { type: 'hidden', name: FIELD, value: token }).prependTo($f);
            });

            $('form').on('submit', function (e) {
                if ($(this).hasClass('.x-validate')) {
                    if (!this.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).addClass('was-validated');
                        return;
                    }
                    $(this).addClass('was-validated');
                }

                if ($(this).hasClass('x-show-page-spinner')) {
                    window.xui__page_spinner.show();
                }
            });            
        });
    </script>
</body>

</html>