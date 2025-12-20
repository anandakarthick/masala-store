@extends('layouts.admin')

@section('title', 'Banner Generator')

@section('content')
<div class="p-4" x-data="bannerGenerator()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🎨 Creative Banner Generator</h1>
            <p class="text-gray-600 text-sm mt-1">Create stunning banners for WhatsApp, Instagram, Facebook & more</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Panel - Controls -->
        <div class="xl:col-span-1 space-y-4 max-h-screen overflow-y-auto pb-20">
            <!-- Platform & Size -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-mobile-alt mr-2 text-purple-600"></i>
                    Platform & Size
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($bannerSizes as $key => $size)
                        <button type="button" @click="setSize('{{ $key }}')"
                                :class="selectedSize === '{{ $key }}' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:border-purple-400'"
                                class="px-2 py-2 text-xs border rounded-lg transition flex items-center justify-center gap-1">
                            <i class="{{ $size['icon'] }}"></i>
                            <span class="truncate">{{ $size['label'] }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 text-center" x-text="bannerSizes[selectedSize]?.width + ' × ' + bannerSizes[selectedSize]?.height + ' px'"></p>
            </div>

            <!-- Creative Templates -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-magic mr-2 text-purple-600"></i>
                    Creative Template
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="(tpl, key) in templates" :key="key">
                        <button type="button" @click="setTemplate(key)"
                                :class="selectedTemplate === key ? 'ring-2 ring-purple-500 bg-purple-50' : 'hover:bg-gray-50'"
                                class="p-3 border rounded-lg transition text-left">
                            <div class="text-lg mb-1" x-text="tpl.icon"></div>
                            <div class="text-xs font-medium text-gray-800" x-text="tpl.name"></div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Color Theme -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-palette mr-2 text-purple-600"></i>
                    Color Theme
                </h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($colorThemes as $key => $theme)
                        <button type="button" @click="setTheme('{{ $key }}')"
                                :class="selectedTheme === '{{ $key }}' ? 'ring-2 ring-offset-2 ring-purple-500 scale-105' : ''"
                                class="h-12 rounded-lg transition transform hover:scale-105"
                                style="background: linear-gradient(135deg, {{ $theme['primary'] }} 0%, {{ $theme['secondary'] }} 100%)"
                                title="{{ $theme['label'] }}">
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center" x-text="colorThemes[selectedTheme]?.label"></p>
            </div>

            <!-- Product Selection -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-box-open mr-2 text-purple-600"></i>
                    Select Product
                </h3>
                <select x-model="selectedProduct" @change="loadProductDetails()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Choose a product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <button type="button" x-show="selectedProduct" @click="clearProduct()" class="mt-2 text-xs text-red-500 hover:text-red-700">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
            </div>

            <!-- Custom Text -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-pen-fancy mr-2 text-purple-600"></i>
                    Custom Text
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Headline</label>
                        <input type="text" x-model="headline" @input="renderCanvas()" placeholder="Amazing Product!" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tagline</label>
                        <input type="text" x-model="subheadline" @input="renderCanvas()" placeholder="Best quality guaranteed" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Offer Badge</label>
                        <input type="text" x-model="offerText" @input="renderCanvas()" placeholder="50% OFF" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Button Text</label>
                        <input type="text" x-model="ctaText" @input="renderCanvas()" placeholder="Shop Now" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Display Options -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-eye mr-2 text-purple-600"></i>
                    Show / Hide
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" x-model="showProductImage" @change="renderCanvas()" class="rounded text-purple-600">
                        <span class="text-sm">Product Image</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" x-model="showPrice" @change="renderCanvas()" class="rounded text-purple-600">
                        <span class="text-sm">Price</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" x-model="showLogo" @change="renderCanvas()" class="rounded text-purple-600">
                        <span class="text-sm">Logo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" x-model="showContact" @change="renderCanvas()" class="rounded text-purple-600">
                        <span class="text-sm">Contact</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" x-model="showDecorations" @change="renderCanvas()" class="rounded text-purple-600">
                        <span class="text-sm">Effects</span>
                    </label>
                </div>
            </div>

            <!-- Custom Background -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-image mr-2 text-purple-600"></i>
                    Custom Background
                </h3>
                <input type="file" @change="handleBackgroundUpload($event)" accept="image/*" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                <button type="button" x-show="customBgImage" @click="customBgImage = null; renderCanvas()" class="mt-2 text-xs text-red-500">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </div>
        </div>

        <!-- Right Panel - Preview -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-lg shadow p-4 sticky top-4">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-sparkles mr-2 text-purple-600"></i>
                        Live Preview
                    </h3>
                    <div class="flex gap-2">
                        <button @click="downloadBanner('png')" class="px-5 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full hover:shadow-lg transition text-sm flex items-center gap-2">
                            <i class="fas fa-download"></i> PNG
                        </button>
                        <button @click="downloadBanner('jpg')" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-full hover:shadow-lg transition text-sm flex items-center gap-2">
                            <i class="fas fa-download"></i> JPG
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-center items-center bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl p-6 min-h-[480px]">
                    <canvas id="bannerCanvas" class="shadow-2xl rounded-lg transition-all duration-300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bannerGenerator() {
    return {
        bannerSizes: @json($bannerSizes),
        colorThemes: @json($colorThemes),
        businessName: @json($businessName),
        businessPhone: @json($businessPhone),
        businessWebsite: window.location.host,
        logoUrl: @json($logoUrl),
        
        templates: {
            'modern': { name: 'Modern Clean', icon: '✨' },
            'bold': { name: 'Bold Impact', icon: '💥' },
            'elegant': { name: 'Elegant', icon: '👑' },
            'festive': { name: 'Festive Sale', icon: '🎉' },
            'minimal': { name: 'Minimal', icon: '◻️' },
            'gradient': { name: 'Gradient Wave', icon: '🌊' },
            'neon': { name: 'Neon Glow', icon: '💡' },
            'organic': { name: 'Organic', icon: '🌿' },
        },
        
        selectedSize: 'whatsapp_status',
        selectedTheme: 'green_fresh',
        selectedTemplate: 'modern',
        selectedProduct: '',
        
        headline: '',
        subheadline: '',
        offerText: '',
        ctaText: 'Shop Now',
        
        showPrice: true,
        showLogo: true,
        showContact: true,
        showProductImage: true,
        showDecorations: true,
        
        productData: null,
        customBgImage: null,
        logoImage: null,
        productImage: null,
        
        init() {
            if (this.logoUrl) {
                this.logoImage = new Image();
                this.logoImage.crossOrigin = 'anonymous';
                this.logoImage.onload = () => this.renderCanvas();
                this.logoImage.src = this.logoUrl;
            }
            this.$nextTick(() => this.renderCanvas());
        },
        
        setSize(s) { this.selectedSize = s; this.renderCanvas(); },
        setTheme(t) { this.selectedTheme = t; this.renderCanvas(); },
        setTemplate(t) { this.selectedTemplate = t; this.renderCanvas(); },
        
        isVertical() {
            const s = this.bannerSizes[this.selectedSize];
            return s && s.height > s.width;
        },
        
        clearProduct() {
            this.selectedProduct = '';
            this.productData = null;
            this.productImage = null;
            this.headline = '';
            this.subheadline = '';
            this.offerText = '';
            this.renderCanvas();
        },
        
        async loadProductDetails() {
            if (!this.selectedProduct) { this.clearProduct(); return; }
            
            try {
                const res = await fetch(`{{ route('admin.banner-generator.product-details') }}?product_id=${this.selectedProduct}`);
                this.productData = await res.json();
                this.headline = this.productData.name || '';
                this.subheadline = (this.productData.description || '').substring(0, 45);
                this.offerText = this.productData.discount_percentage > 0 ? Math.round(this.productData.discount_percentage) + '% OFF' : '';
                
                if (this.productData.image_url) {
                    this.productImage = new Image();
                    this.productImage.crossOrigin = 'anonymous';
                    this.productImage.onload = () => this.renderCanvas();
                    this.productImage.src = this.productData.image_url;
                } else {
                    this.productImage = null;
                    this.renderCanvas();
                }
            } catch (e) { console.error(e); }
        },
        
        handleBackgroundUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.customBgImage = new Image();
                    this.customBgImage.onload = () => this.renderCanvas();
                    this.customBgImage.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        renderCanvas() {
            const canvas = document.getElementById('bannerCanvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const size = this.bannerSizes[this.selectedSize];
            const theme = this.colorThemes[this.selectedTheme];
            const W = size.width, H = size.height;
            
            canvas.width = W;
            canvas.height = H;
            
            const maxW = 480, maxH = 580;
            const scale = Math.min(maxW / W, maxH / H, 1);
            canvas.style.width = (W * scale) + 'px';
            canvas.style.height = (H * scale) + 'px';
            
            // Draw based on template
            this.drawTemplate(ctx, W, H, theme);
        },
        
        drawTemplate(ctx, W, H, theme) {
            const tpl = this.selectedTemplate;
            const p = theme.primary, s = theme.secondary;
            const isVert = this.isVertical();
            const base = Math.min(W, H);
            const pad = base * 0.045;
            
            // Clear
            ctx.clearRect(0, 0, W, H);
            
            // Background based on template
            if (this.customBgImage) {
                this.drawCoverImage(ctx, this.customBgImage, W, H);
                ctx.fillStyle = 'rgba(0,0,0,0.5)';
                ctx.fillRect(0, 0, W, H);
            } else {
                this.drawBackground(ctx, W, H, theme, tpl);
            }
            
            // Decorations
            if (this.showDecorations && !this.customBgImage) {
                this.drawDecorations(ctx, W, H, theme, tpl);
            }
            
            // Content
            this.drawContent(ctx, W, H, theme, tpl, pad, base, isVert);
        },
        
        drawBackground(ctx, W, H, theme, tpl) {
            const p = theme.primary, s = theme.secondary;
            let grad;
            
            switch(tpl) {
                case 'bold':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(0.5, s);
                    grad.addColorStop(1, p);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    break;
                    
                case 'elegant':
                    ctx.fillStyle = '#1a1a2e';
                    ctx.fillRect(0, 0, W, H);
                    grad = ctx.createRadialGradient(W*0.3, H*0.3, 0, W*0.5, H*0.5, W*0.8);
                    grad.addColorStop(0, p + '40');
                    grad.addColorStop(1, 'transparent');
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    break;
                    
                case 'festive':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, '#ff6b6b');
                    grad.addColorStop(0.5, '#feca57');
                    grad.addColorStop(1, '#ff9ff3');
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    break;
                    
                case 'minimal':
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, W, H);
                    ctx.fillStyle = p;
                    ctx.fillRect(0, H * 0.85, W, H * 0.15);
                    break;
                    
                case 'gradient':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    // Wave
                    ctx.fillStyle = 'rgba(255,255,255,0.1)';
                    ctx.beginPath();
                    ctx.moveTo(0, H * 0.6);
                    ctx.bezierCurveTo(W * 0.3, H * 0.5, W * 0.7, H * 0.7, W, H * 0.55);
                    ctx.lineTo(W, H);
                    ctx.lineTo(0, H);
                    ctx.closePath();
                    ctx.fill();
                    break;
                    
                case 'neon':
                    ctx.fillStyle = '#0d0d0d';
                    ctx.fillRect(0, 0, W, H);
                    break;
                    
                case 'organic':
                    grad = ctx.createRadialGradient(W*0.5, H*0.5, 0, W*0.5, H*0.5, W*0.7);
                    grad.addColorStop(0, '#a8e6cf');
                    grad.addColorStop(1, '#3d5a45');
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    break;
                    
                default: // modern
                    grad = ctx.createRadialGradient(W*0.5, H*0.4, 0, W*0.5, H*0.5, W*0.8);
                    grad.addColorStop(0, s);
                    grad.addColorStop(1, p);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
            }
        },
        
        drawDecorations(ctx, W, H, theme, tpl) {
            const p = theme.primary, s = theme.secondary;
            
            switch(tpl) {
                case 'modern':
                    // Floating circles
                    ctx.fillStyle = 'rgba(255,255,255,0.1)';
                    ctx.beginPath(); ctx.arc(W * 0.9, H * 0.1, W * 0.15, 0, Math.PI * 2); ctx.fill();
                    ctx.beginPath(); ctx.arc(W * 0.1, H * 0.85, W * 0.1, 0, Math.PI * 2); ctx.fill();
                    ctx.fillStyle = s + '30';
                    ctx.beginPath(); ctx.arc(W * 0.85, H * 0.8, W * 0.08, 0, Math.PI * 2); ctx.fill();
                    break;
                    
                case 'bold':
                    // Diagonal stripes
                    ctx.strokeStyle = 'rgba(255,255,255,0.08)';
                    ctx.lineWidth = W * 0.02;
                    for (let i = -H; i < W + H; i += W * 0.08) {
                        ctx.beginPath();
                        ctx.moveTo(i, 0);
                        ctx.lineTo(i + H, H);
                        ctx.stroke();
                    }
                    // Corner accent
                    ctx.fillStyle = 'rgba(255,255,255,0.15)';
                    ctx.beginPath();
                    ctx.moveTo(0, 0);
                    ctx.lineTo(W * 0.4, 0);
                    ctx.lineTo(0, H * 0.25);
                    ctx.closePath();
                    ctx.fill();
                    break;
                    
                case 'elegant':
                    // Gold accents
                    ctx.strokeStyle = '#d4af37';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(W * 0.05, H * 0.05, W * 0.9, H * 0.9);
                    // Corner ornaments
                    ctx.fillStyle = '#d4af37';
                    this.drawDiamond(ctx, W * 0.05, H * 0.05, 15);
                    this.drawDiamond(ctx, W * 0.95, H * 0.05, 15);
                    this.drawDiamond(ctx, W * 0.05, H * 0.95, 15);
                    this.drawDiamond(ctx, W * 0.95, H * 0.95, 15);
                    break;
                    
                case 'festive':
                    // Confetti
                    const colors = ['#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', '#1dd1a1', '#ffffff'];
                    for (let i = 0; i < 40; i++) {
                        ctx.fillStyle = colors[Math.floor(Math.random() * colors.length)];
                        ctx.globalAlpha = 0.6;
                        const x = Math.random() * W;
                        const y = Math.random() * H;
                        const size = Math.random() * 12 + 4;
                        ctx.save();
                        ctx.translate(x, y);
                        ctx.rotate(Math.random() * Math.PI);
                        ctx.fillRect(-size/2, -size/4, size, size/2);
                        ctx.restore();
                    }
                    ctx.globalAlpha = 1;
                    break;
                    
                case 'minimal':
                    // Simple line
                    ctx.strokeStyle = p;
                    ctx.lineWidth = 3;
                    ctx.beginPath();
                    ctx.moveTo(W * 0.1, H * 0.15);
                    ctx.lineTo(W * 0.3, H * 0.15);
                    ctx.stroke();
                    break;
                    
                case 'gradient':
                    // Additional waves
                    ctx.fillStyle = 'rgba(255,255,255,0.07)';
                    ctx.beginPath();
                    ctx.moveTo(0, H * 0.7);
                    ctx.bezierCurveTo(W * 0.4, H * 0.6, W * 0.6, H * 0.8, W, H * 0.65);
                    ctx.lineTo(W, H);
                    ctx.lineTo(0, H);
                    ctx.closePath();
                    ctx.fill();
                    break;
                    
                case 'neon':
                    // Neon border glow
                    ctx.shadowColor = p;
                    ctx.shadowBlur = 30;
                    ctx.strokeStyle = p;
                    ctx.lineWidth = 3;
                    ctx.strokeRect(W * 0.05, H * 0.05, W * 0.9, H * 0.9);
                    ctx.shadowBlur = 0;
                    // Neon circles
                    ctx.shadowColor = s;
                    ctx.shadowBlur = 20;
                    ctx.strokeStyle = s;
                    ctx.beginPath(); ctx.arc(W * 0.85, H * 0.15, W * 0.08, 0, Math.PI * 2); ctx.stroke();
                    ctx.beginPath(); ctx.arc(W * 0.15, H * 0.85, W * 0.06, 0, Math.PI * 2); ctx.stroke();
                    ctx.shadowBlur = 0;
                    break;
                    
                case 'organic':
                    // Leaf shapes
                    ctx.fillStyle = 'rgba(255,255,255,0.1)';
                    this.drawLeaf(ctx, W * 0.1, H * 0.2, 40, -30);
                    this.drawLeaf(ctx, W * 0.9, H * 0.15, 35, 30);
                    this.drawLeaf(ctx, W * 0.85, H * 0.85, 30, 150);
                    this.drawLeaf(ctx, W * 0.08, H * 0.8, 25, -150);
                    break;
            }
        },
        
        drawContent(ctx, W, H, theme, tpl, pad, base, isVert) {
            const p = theme.primary, s = theme.secondary;
            
            // Font sizes
            const headSize = Math.round(base * (isVert ? 0.055 : 0.065));
            const subSize = Math.round(base * (isVert ? 0.026 : 0.032));
            const priceSize = Math.round(base * (isVert ? 0.058 : 0.068));
            const oldPriceSize = Math.round(base * (isVert ? 0.032 : 0.038));
            const btnSize = Math.round(base * (isVert ? 0.032 : 0.038));
            const badgeSize = Math.round(base * (isVert ? 0.032 : 0.038));
            const contactSize = Math.round(base * 0.022);
            
            // Text colors based on template
            let textColor = '#ffffff';
            let accentColor = '#ffffff';
            if (tpl === 'minimal') { textColor = '#333333'; accentColor = p; }
            if (tpl === 'elegant') { accentColor = '#d4af37'; }
            if (tpl === 'neon') { accentColor = s; }
            
            let y = pad;
            
            // ===== TOP: Logo & Badge =====
            const topH = base * 0.065;
            
            if (this.showLogo && this.logoImage?.complete) {
                const logoH = topH;
                const logoW = (this.logoImage.width / this.logoImage.height) * logoH;
                
                if (tpl === 'elegant') {
                    ctx.fillStyle = '#d4af37';
                    this.roundRect(ctx, pad - 3, pad - 3, logoW + 6, logoH + 6, 6);
                    ctx.fill();
                }
                ctx.fillStyle = '#fff';
                this.roundRect(ctx, pad, pad, logoW, logoH, 5);
                ctx.fill();
                ctx.save();
                this.roundRect(ctx, pad, pad, logoW, logoH, 5);
                ctx.clip();
                ctx.drawImage(this.logoImage, pad, pad, logoW, logoH);
                ctx.restore();
            }
            
            // Offer badge
            if (this.offerText) {
                ctx.font = `bold ${badgeSize}px Arial`;
                const bw = ctx.measureText(this.offerText).width + badgeSize * 1.2;
                const bh = badgeSize * 1.7;
                const bx = W - pad - bw;
                const by = pad;
                
                // Badge style based on template
                if (tpl === 'neon') {
                    ctx.shadowColor = '#ff0055';
                    ctx.shadowBlur = 15;
                    ctx.fillStyle = '#ff0055';
                } else if (tpl === 'elegant') {
                    ctx.fillStyle = '#d4af37';
                } else if (tpl === 'festive') {
                    ctx.fillStyle = '#ff3838';
                } else {
                    ctx.fillStyle = '#e53935';
                }
                
                if (tpl === 'bold') {
                    // Ribbon style
                    ctx.beginPath();
                    ctx.moveTo(bx, by);
                    ctx.lineTo(bx + bw, by);
                    ctx.lineTo(bx + bw + 10, by + bh/2);
                    ctx.lineTo(bx + bw, by + bh);
                    ctx.lineTo(bx, by + bh);
                    ctx.lineTo(bx + 10, by + bh/2);
                    ctx.closePath();
                    ctx.fill();
                } else {
                    this.roundRect(ctx, bx, by, bw, bh, bh/2);
                    ctx.fill();
                }
                
                ctx.shadowBlur = 0;
                ctx.fillStyle = '#fff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(this.offerText, bx + bw/2, by + bh/2);
            }
            
            y = pad + topH + pad * 1.5;
            
            // ===== MIDDLE: Product, Headline, Price, CTA =====
            const botH = this.showContact ? base * 0.08 : pad;
            const midH = H - y - botH - pad;
            const centerY = y + midH / 2;
            
            // Calculate content height
            let contentH = 0;
            const imgH = isVert ? base * 0.2 : base * 0.15;
            const gap = pad * 0.6;
            
            if (this.showProductImage && this.productImage?.complete) contentH += imgH + gap;
            contentH += headSize * 1.3;
            if (this.subheadline || this.productData?.description) contentH += subSize * 2 + gap * 0.8;
            if (this.showPrice && this.productData) contentH += priceSize * 2.2 + gap;
            if (this.ctaText) contentH += btnSize * 2.5;
            
            let drawY = centerY - contentH / 2;
            
            // Product Image
            if (this.showProductImage && this.productImage?.complete) {
                const maxH = imgH, maxW = W * 0.5;
                const ratio = this.productImage.width / this.productImage.height;
                let iW = ratio > maxW/maxH ? maxW : maxH * ratio;
                let iH = ratio > maxW/maxH ? maxW / ratio : maxH;
                const ix = (W - iW) / 2;
                
                // Image style based on template
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 20;
                ctx.shadowOffsetY = 8;
                
                if (tpl === 'elegant') {
                    ctx.fillStyle = '#d4af37';
                    this.roundRect(ctx, ix - 4, drawY - 4, iW + 8, iH + 8, 12);
                    ctx.fill();
                } else if (tpl === 'neon') {
                    ctx.shadowColor = p;
                    ctx.shadowBlur = 25;
                    ctx.strokeStyle = p;
                    ctx.lineWidth = 3;
                    this.roundRect(ctx, ix - 2, drawY - 2, iW + 4, iH + 4, 12);
                    ctx.stroke();
                } else {
                    ctx.fillStyle = 'rgba(255,255,255,0.2)';
                    this.roundRect(ctx, ix - 4, drawY - 4, iW + 8, iH + 8, 14);
                    ctx.fill();
                }
                
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                ctx.save();
                this.roundRect(ctx, ix, drawY, iW, iH, 10);
                ctx.clip();
                ctx.drawImage(this.productImage, ix, drawY, iW, iH);
                ctx.restore();
                
                drawY += iH + gap * 1.2;
            }
            
            // Headline
            const headText = this.headline || this.productData?.name || 'Product Name';
            ctx.font = `bold ${headSize}px Arial`;
            ctx.fillStyle = textColor;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            
            if (tpl === 'neon') {
                ctx.shadowColor = accentColor;
                ctx.shadowBlur = 15;
            } else {
                ctx.shadowColor = 'rgba(0,0,0,0.4)';
                ctx.shadowBlur = 6;
            }
            
            const headLines = this.wrapText(ctx, headText, W - pad * 3.5);
            headLines.forEach((line, i) => ctx.fillText(line, W/2, drawY + i * headSize * 1.2));
            drawY += headLines.length * headSize * 1.2 + gap * 0.8;
            ctx.shadowBlur = 0;
            
            // Subheadline
            const subText = this.subheadline || (this.productData?.description || '').substring(0, 45);
            if (subText) {
                ctx.font = `${subSize}px Arial`;
                ctx.fillStyle = tpl === 'minimal' ? '#666' : 'rgba(255,255,255,0.85)';
                const subLines = this.wrapText(ctx, subText, W - pad * 4.5);
                subLines.forEach((line, i) => ctx.fillText(line, W/2, drawY + i * subSize * 1.4));
                drawY += subLines.length * subSize * 1.4 + gap;
            }
            
            // Price
            if (this.showPrice && this.productData) {
                const curr = parseFloat(this.productData.discount_price || this.productData.price);
                const orig = parseFloat(this.productData.price);
                const hasDisc = this.productData.discount_price && curr !== orig;
                
                // Price box
                const boxW = W * 0.5, boxH = priceSize * 2;
                const boxX = (W - boxW) / 2;
                
                if (tpl === 'neon') {
                    ctx.strokeStyle = accentColor;
                    ctx.lineWidth = 2;
                    ctx.shadowColor = accentColor;
                    ctx.shadowBlur = 10;
                    this.roundRect(ctx, boxX, drawY, boxW, boxH, boxH/2);
                    ctx.stroke();
                    ctx.shadowBlur = 0;
                } else if (tpl !== 'minimal') {
                    ctx.fillStyle = 'rgba(255,255,255,0.15)';
                    this.roundRect(ctx, boxX, drawY, boxW, boxH, boxH/2);
                    ctx.fill();
                }
                
                ctx.textBaseline = 'middle';
                const boxCY = drawY + boxH / 2;
                
                if (hasDisc) {
                    const oldT = '₹' + Math.round(orig);
                    const newT = '₹' + Math.round(curr);
                    
                    ctx.font = `${oldPriceSize}px Arial`;
                    const oldW = ctx.measureText(oldT).width;
                    ctx.font = `bold ${priceSize}px Arial`;
                    const newW = ctx.measureText(newT).width;
                    
                    const totalW = oldW + 20 + newW;
                    const startX = (W - totalW) / 2;
                    
                    // Old price
                    ctx.font = `${oldPriceSize}px Arial`;
                    ctx.fillStyle = tpl === 'minimal' ? '#999' : 'rgba(255,255,255,0.5)';
                    ctx.textAlign = 'left';
                    ctx.fillText(oldT, startX, boxCY);
                    
                    ctx.strokeStyle = ctx.fillStyle;
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(startX - 2, boxCY);
                    ctx.lineTo(startX + oldW + 2, boxCY);
                    ctx.stroke();
                    
                    // New price
                    ctx.font = `bold ${priceSize}px Arial`;
                    ctx.fillStyle = tpl === 'neon' ? accentColor : (tpl === 'minimal' ? p : '#fff');
                    if (tpl === 'neon') { ctx.shadowColor = accentColor; ctx.shadowBlur = 10; }
                    ctx.fillText(newT, startX + oldW + 20, boxCY);
                    ctx.shadowBlur = 0;
                } else {
                    ctx.font = `bold ${priceSize}px Arial`;
                    ctx.fillStyle = tpl === 'neon' ? accentColor : (tpl === 'minimal' ? p : '#fff');
                    ctx.textAlign = 'center';
                    if (tpl === 'neon') { ctx.shadowColor = accentColor; ctx.shadowBlur = 10; }
                    ctx.fillText('₹' + Math.round(curr), W/2, boxCY);
                    ctx.shadowBlur = 0;
                }
                
                drawY += boxH + gap;
            }
            
            // CTA Button
            if (this.ctaText) {
                ctx.font = `bold ${btnSize}px Arial`;
                const btnText = this.ctaText + '  →';
                const btnW = ctx.measureText(btnText).width + btnSize * 2.2;
                const btnH = btnSize * 2.2;
                const btnX = (W - btnW) / 2;
                
                ctx.shadowColor = 'rgba(0,0,0,0.25)';
                ctx.shadowBlur = 12;
                ctx.shadowOffsetY = 4;
                
                if (tpl === 'neon') {
                    ctx.fillStyle = 'transparent';
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 2;
                    ctx.shadowColor = '#fff';
                    ctx.shadowBlur = 10;
                    this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH/2);
                    ctx.stroke();
                    ctx.fillStyle = '#fff';
                } else if (tpl === 'elegant') {
                    ctx.fillStyle = '#d4af37';
                    this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH/2);
                    ctx.fill();
                    ctx.fillStyle = '#1a1a2e';
                } else if (tpl === 'minimal') {
                    ctx.fillStyle = p;
                    this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH/2);
                    ctx.fill();
                    ctx.fillStyle = '#fff';
                } else {
                    ctx.fillStyle = '#fff';
                    this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH/2);
                    ctx.fill();
                    ctx.fillStyle = p;
                }
                
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(btnText, W/2, drawY + btnH/2);
            }
            
            // ===== BOTTOM: Contact =====
            if (this.showContact && this.businessPhone) {
                const cH = botH * 0.7;
                const cW = W * 0.42;
                const cX = pad;
                const cY = H - pad - cH;
                
                ctx.fillStyle = tpl === 'minimal' ? 'rgba(0,0,0,0.05)' : 'rgba(0,0,0,0.25)';
                this.roundRect(ctx, cX, cY, cW, cH, 8);
                ctx.fill();
                
                ctx.font = `${contactSize}px Arial`;
                ctx.fillStyle = tpl === 'minimal' ? '#666' : '#fff';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                ctx.fillText('📞 ' + this.businessPhone, cX + 10, cY + cH * 0.35);
                ctx.fillText('🌐 ' + this.businessWebsite, cX + 10, cY + cH * 0.7);
            }
        },
        
        // Helper functions
        drawCoverImage(ctx, img, W, H) {
            const r = img.width / img.height;
            const cr = W / H;
            let dw, dh, dx, dy;
            if (r > cr) { dh = H; dw = H * r; dx = (W - dw) / 2; dy = 0; }
            else { dw = W; dh = W / r; dx = 0; dy = (H - dh) / 2; }
            ctx.drawImage(img, dx, dy, dw, dh);
        },
        
        drawDiamond(ctx, x, y, size) {
            ctx.beginPath();
            ctx.moveTo(x, y - size);
            ctx.lineTo(x + size, y);
            ctx.lineTo(x, y + size);
            ctx.lineTo(x - size, y);
            ctx.closePath();
            ctx.fill();
        },
        
        drawLeaf(ctx, x, y, size, angle) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(angle * Math.PI / 180);
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.bezierCurveTo(size * 0.5, -size * 0.3, size, -size * 0.1, size, 0);
            ctx.bezierCurveTo(size, size * 0.1, size * 0.5, size * 0.3, 0, 0);
            ctx.fill();
            ctx.restore();
        },
        
        roundRect(ctx, x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + r);
            ctx.lineTo(x + w, y + h - r);
            ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            ctx.lineTo(x + r, y + h);
            ctx.quadraticCurveTo(x, y + h, x, y + h - r);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath();
        },
        
        wrapText(ctx, text, maxW) {
            const words = text.split(' ');
            const lines = [];
            let line = '';
            words.forEach(w => {
                const test = line + (line ? ' ' : '') + w;
                if (ctx.measureText(test).width > maxW && line) {
                    lines.push(line);
                    line = w;
                } else line = test;
            });
            if (line) lines.push(line);
            return lines.length ? lines : [text];
        },
        
        downloadBanner(fmt) {
            const c = document.getElementById('bannerCanvas');
            const a = document.createElement('a');
            a.download = `banner_${this.selectedTemplate}_${Date.now()}.${fmt}`;
            a.href = c.toDataURL(fmt === 'png' ? 'image/png' : 'image/jpeg', 0.95);
            a.click();
        }
    };
}
</script>
@endsection
