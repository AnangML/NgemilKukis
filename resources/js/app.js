import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: JSON.parse(localStorage.getItem('ngemilkukis_cart') || '[]'),
        isOpen: false,
        isGift: false,
        giftData: {
            templateId: 1,
            templateName: 'Happy Birthday Sweet Moments',
            recipientName: '',
            senderName: '',
            message: 'Selamat ulang tahun! Semoga harimu semanis dan sehangat kukis ini.',
            color: 'amber'
        },

        save() {
            localStorage.setItem('ngemilkukis_cart', JSON.stringify(this.items));
        },

        addItem(product, quantity = 1) {
            const existing = this.items.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    type: product.type,
                    quantity: quantity
                });
            }
            this.save();
            this.isOpen = true;
        },

        removeItem(productId) {
            this.items = this.items.filter(item => item.id !== productId);
            this.save();
        },

        updateQuantity(productId, quantity) {
            if (quantity <= 0) {
                this.removeItem(productId);
                return;
            }
            const item = this.items.find(item => item.id === productId);
            if (item) {
                item.quantity = quantity;
                this.save();
            }
        },

        clearCart() {
            this.items = [];
            this.save();
        },

        get count() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },

        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    });
});

window.Alpine = Alpine;
Alpine.start();
