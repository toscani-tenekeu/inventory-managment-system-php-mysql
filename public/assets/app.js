(() => {
    'use strict';

    const root = document.documentElement;
    const storageKey = 'ims-theme';
    const storedTheme = localStorage.getItem(storageKey);
    if (storedTheme === 'light' || storedTheme === 'dark') {
        root.dataset.theme = storedTheme;
    }

    const preferredDark = () => window.matchMedia('(prefers-color-scheme: dark)').matches;
    const resolvedTheme = () => {
        if (root.dataset.theme === 'system') {
            return preferredDark() ? 'dark' : 'light';
        }
        return root.dataset.theme === 'dark' ? 'dark' : 'light';
    };

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = resolvedTheme() === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem(storageKey, next);
        });
    });

    const closeSidebar = () => document.body.classList.remove('sidebar-open');
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
    });
    document.querySelectorAll('[data-sidebar-close]').forEach((button) => {
        button.addEventListener('click', closeSidebar);
    });
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    document.querySelectorAll('[data-alert-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.alert')?.remove());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Confirm?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.closest('.password-field')?.querySelector('input');
            if (!input) {
                return;
            }
            input.type = input.type === 'password' ? 'text' : 'password';
            button.textContent = input.type === 'password' ? '○' : '●';
        });
    });

    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    document.querySelectorAll('[data-movement-form]').forEach((form) => {
        const type = form.querySelector('[data-movement-type]');
        const product = form.querySelector('[data-movement-product]');
        const partner = form.querySelector('[data-movement-partner]');
        const quantity = form.querySelector('[data-movement-quantity]');
        const price = form.querySelector('[data-movement-price]');
        const total = form.querySelector('[data-movement-total]');
        const currency = form.dataset.currency || 'XAF';
        const decimals = Number(form.dataset.currencyDecimals || 0);

        const formatMoney = (value) => {
            try {
                return new Intl.NumberFormat(root.lang || 'fr', {
                    style: 'currency',
                    currency,
                    currencyDisplay: 'code',
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                }).format(value);
            } catch {
                return value.toFixed(decimals) + ' ' + currency;
            }
        };

        const updateTotal = () => {
            const amount = (Number(quantity?.value) || 0) * (Number(price?.value) || 0);
            if (total) {
                total.textContent = formatMoney(amount);
            }
        };

        const updatePrice = () => {
            const option = product?.selectedOptions?.[0];
            if (!option || !price) {
                updateTotal();
                return;
            }
            const movementType = type?.value || 'purchase';
            const usesSalePrice = movementType === 'sale' || movementType === 'customer_return';
            const suggested = usesSalePrice ? option.dataset.sale : option.dataset.cost;
            if (suggested !== undefined && suggested !== '') {
                price.value = suggested;
            }
            if (quantity) {
                const isOutbound = ['sale', 'supplier_return', 'adjustment_out'].includes(movementType);
                if (isOutbound && option.dataset.stock !== undefined) {
                    quantity.max = option.dataset.stock;
                } else {
                    quantity.removeAttribute('max');
                }
            }
            updateTotal();
        };

        const updatePartners = () => {
            if (!partner || !type) {
                return;
            }
            const movementType = type.value;
            const expectedPartner = ['purchase', 'supplier_return'].includes(movementType)
                ? 'supplier'
                : (['sale', 'customer_return'].includes(movementType) ? 'customer' : '');
            const requiresPartner = expectedPartner !== '';
            partner.disabled = !requiresPartner;
            partner.required = requiresPartner;

            partner.querySelectorAll('[data-partner-type]').forEach((option) => {
                const visible = option.dataset.partnerType === expectedPartner;
                option.disabled = !visible;
                option.hidden = !visible;
            });
            partner.querySelectorAll('[data-partner-group]').forEach((group) => {
                group.disabled = group.dataset.partnerGroup !== expectedPartner;
                group.hidden = group.dataset.partnerGroup !== expectedPartner;
            });

            const selected = partner.selectedOptions?.[0];
            if (!requiresPartner || (selected?.dataset.partnerType && selected.dataset.partnerType !== expectedPartner)) {
                partner.value = '';
            }
        };

        type?.addEventListener('change', () => {
            updatePartners();
            updatePrice();
        });
        product?.addEventListener('change', updatePrice);
        quantity?.addEventListener('input', updateTotal);
        price?.addEventListener('input', updateTotal);

        updatePartners();
        updatePrice();
    });
})();
