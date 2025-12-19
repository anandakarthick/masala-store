@extends('layouts.admin')

@section('title', 'Banner Generator')

@section('content')
<div class="p-4" x-data="bannerGenerator()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Social Media Banner Generator</h1>
            <p class="text-gray-600 text-sm mt-1">Create professional banners for WhatsApp, Instagram, Facebook, Google Ads & more</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Panel - Controls -->
        <div class="xl:col-span-1 space-y-4 max-h-screen overflow-y-auto pb-20">
            <!-- Platform & Size Selection -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-expand-arrows-alt mr-2 text-green-600"></i>
                    Platform & Size
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($bannerSizes as $key => $size)
                        <button type="button"
                                @click="setSize('{{ $key }}')"
                                :class="selectedSize === '{{ $key }}' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:border-green-500'"
                                class="px-2 py-2 text-xs border rounded-lg transition flex items-center justify-center gap-1">
                            <i class="{{ $size['icon'] }}"></i>
                            <span class="truncate">{{ $size['label'] }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 text-xs text-gray-500 text-center" x-show="selectedSize">
                    Size: <span x-text="bannerSizes[selectedSize]?.width"></span> x <span x-text="bannerSizes[selectedSize]?.height"></span> px
                </div>
            </div>

            <!-- Color Theme -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-fill-drip mr-2 text-green-600"></i>
                    Color Theme
                </h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($colorThemes as $key => $theme)
                        <button type="button"
                                @click="setTheme('{{ $key }}')"
                                :class="selectedTheme === '{{ $key }}' ? 'ring-2 ring-offset-2 ring-green-500' : ''"
                                class="h-10 rounded-lg transition relative overflow-hidden"
                                style="background: linear-gradient(135deg, {{ $theme['primary'] }} 50%, {{ $theme['secondary'] }} 50%)"
                                title="{{ $theme['label'] }}">
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center" x-text="colorThemes[selectedTheme]?.label"></p>
            </div>

            <!-- Background Pattern -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-th mr-2 text-green-600"></i>
                    Background Design
                </h3>
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="(pattern, key) in backgroundPatterns" :key="key">
                        <button type="button"
                                @click="setPattern(key)"
                                :class="selectedPattern === key ? 'ring-2 ring-offset-2 ring-green-500' : ''"
                                class="h-12 rounded-lg transition relative overflow-hidden border border-gray-200 text-xs text-white font-medium flex items-center justify-center"
                                :style="'background: linear-gradient(135deg, ' + colorThemes[selectedTheme]?.primary + ', ' + colorThemes[selectedTheme]?.secondary + ')'">
                            <span x-text="pattern.label" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5)"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-box mr-2 text-green-600"></i>
                    Select Product
                </h3>
                <select x-model="selectedProduct" @change="loadProductDetails()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">-- Select a product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <button type="button" x-show="selectedProduct" @click="clearProduct()" class="mt-2 text-xs text-red-600 hover:text-red-700">
                    <i class="fas fa-times mr-1"></i> Clear Product
                </button>
            </div>

            <!-- Custom Text -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-font mr-2 text-green-600"></i>
                    Custom Text
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" x-model="headline" @input="renderCanvas()" placeholder="Enter headline..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subheadline</label>
                        <input type="text" x-model="subheadline" @input="renderCanvas()" placeholder="Enter subheadline..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Offer Badge (e.g., "50% OFF")</label>
                        <input type="text" x-model="offerText" @input="renderCanvas()" placeholder="50% OFF" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" x-model="ctaText" @input="renderCanvas()" placeholder="Shop Now" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Display Options -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-sliders-h mr-2 text-green-600"></i>
                    Display Options
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showProductImage" @change="renderCanvas()" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Product Image</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showPrice" @change="renderCanvas()" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Price</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showLogo" @change="renderCanvas()" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Logo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showContact" @change="renderCanvas()" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Contact Info</span>
                    </label>
                </div>
            </div>

            <!-- Custom Background Image -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-image mr-2 text-green-600"></i>
                    Custom Background
                </h3>
                <input type="file" id="bgUpload" @change="handleBackgroundUpload($event)" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <button type="button" x-show="customBackground" @click="customBackground = null; customBgImage = null; renderCanvas()" class="mt-2 text-xs text-red-600 hover:text-red-700">
                    <i class="fas fa-times mr-1"></i> Remove Background
                </button>
            </div>
        </div>

        <!-- Right Panel - Preview & Download -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-lg shadow p-4 sticky top-4">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-eye mr-2 text-green-600"></i>
                        Live Preview
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" @click="downloadBanner('png')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-2">
                            <i class="fas fa-download"></i> Download PNG
                        </button>
                        <button type="button" @click="downloadBanner('jpg')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-2">
                            <i class="fas fa-download"></i> Download JPG
                        </button>
                    </div>
                </div>
                
                <!-- Canvas Preview Container -->
                <div class="flex justify-center items-center bg-gray-200 rounded-lg p-6 min-h-[450px] overflow-auto">
                    <canvas id="bannerCanvas" class="shadow-2xl rounded-lg" style="max-width: 100%; height: auto;"></canvas>
                </div>
                
                <!-- Tips -->
                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3">
                    <h4 class="font-semibold text-green-800 text-sm mb-2"><i class="fas fa-lightbulb mr-1"></i> Quick Tips</h4>
                    <ul class="text-xs text-green-700 space-y-1">
                        <li>• Select a product to auto-fill name, price & image</li>
                        <li>• Keep headline short for better readability</li>
                        <li>• Use offer badges like "50% OFF" to attract attention</li>
                    </ul>
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
        
        backgroundPatterns: {
            'solid': { label: 'Solid' },
            'radial': { label: 'Radial' },
            'circles': { label: 'Circles' },
            'dots': { label: 'Dots' },
            'diagonal': { label: 'Lines' },
        },
        
        selectedSize: 'instagram_post',
        selectedTheme: 'green_fresh',
        selectedPattern: 'radial',
        selectedProduct: '',
        
        headline: '',
        subheadline: '',
        offerText: '',
        ctaText: 'Shop Now',
        
        showPrice: true,
        showLogo: true,
        showContact: true,
        showProductImage: true,
        
        productData: null,
        customBackground: null,
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
            
            this.$nextTick(() => {
                this.renderCanvas();
            });
        },
        
        setSize(size) { 
            this.selectedSize = size; 
            this.renderCanvas();
        },
        
        setTheme(theme) { 
            this.selectedTheme = theme; 
            this.renderCanvas();
        },
        
        setPattern(pattern) { 
            this.selectedPattern = pattern; 
            this.renderCanvas();
        },
        
        isVertical() {
            const size = this.bannerSizes[this.selectedSize];
            return size && size.height > size.width;
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
            if (!this.selectedProduct) {
                this.productData = null;
                this.productImage = null;
                this.renderCanvas();
                return;
            }
            
            try {
                const response = await fetch(`{{ route('admin.banner-generator.product-details') }}?product_id=${this.selectedProduct}`);
                this.productData = await response.json();
                
                this.headline = this.productData.name || '';
                this.subheadline = (this.productData.description || '').substring(0, 50);
                
                if (this.productData.discount_percentage > 0) {
                    this.offerText = Math.round(this.productData.discount_percentage) + '% OFF';
                } else {
                    this.offerText = '';
                }
                
                if (this.productData.image_url) {
                    this.productImage = new Image();
                    this.productImage.crossOrigin = 'anonymous';
                    this.productImage.onload = () => this.renderCanvas();
                    this.productImage.onerror = () => {
                        this.productImage = null;
                        this.renderCanvas();
                    };
                    this.productImage.src = this.productData.image_url;
                } else {
                    this.productImage = null;
                    this.renderCanvas();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        handleBackgroundUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.customBackground = e.target.result;
                    this.customBgImage = new Image();
                    this.customBgImage.onload = () => this.renderCanvas();
                    this.customBgImage.src = this.customBackground;
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
            
            const W = size.width;
            const H = size.height;
            
            canvas.width = W;
            canvas.height = H;
            
            // Display scale
            const maxDisplayWidth = 500;
            const maxDisplayHeight = 600;
            const displayScale = Math.min(maxDisplayWidth / W, maxDisplayHeight / H, 1);
            canvas.style.width = (W * displayScale) + 'px';
            canvas.style.height = (H * displayScale) + 'px';
            
            // Clear canvas
            ctx.clearRect(0, 0, W, H);
            
            // Draw background
            this.drawBackground(ctx, W, H, theme);
            
            // Draw custom background
            if (this.customBgImage) {
                const imgRatio = this.customBgImage.width / this.customBgImage.height;
                const canvasRatio = W / H;
                let drawW, drawH, drawX, drawY;
                
                if (imgRatio > canvasRatio) {
                    drawH = H;
                    drawW = H * imgRatio;
                    drawX = (W - drawW) / 2;
                    drawY = 0;
                } else {
                    drawW = W;
                    drawH = W / imgRatio;
                    drawX = 0;
                    drawY = (H - drawH) / 2;
                }
                
                ctx.drawImage(this.customBgImage, drawX, drawY, drawW, drawH);
                ctx.fillStyle = 'rgba(0,0,0,0.45)';
                ctx.fillRect(0, 0, W, H);
            }
            
            // Calculate sizes
            const padding = Math.min(W, H) * 0.045;
            const isVert = this.isVertical();
            const baseSize = Math.min(W, H);
            
            // Font sizes - FIXED for consistency
            const headlineSize = Math.round(baseSize * (isVert ? 0.058 : 0.068));
            const subheadlineSize = Math.round(baseSize * (isVert ? 0.028 : 0.034));
            const newPriceSize = Math.round(baseSize * (isVert ? 0.065 : 0.075));
            const oldPriceSize = Math.round(baseSize * (isVert ? 0.038 : 0.045));
            const buttonSize = Math.round(baseSize * (isVert ? 0.034 : 0.04));
            const badgeSize = Math.round(baseSize * (isVert ? 0.034 : 0.04));
            const contactSize = Math.round(baseSize * (isVert ? 0.024 : 0.028));
            const weightSize = Math.round(baseSize * 0.022);
            
            let currentY = padding;
            
            // ========== TOP SECTION: Logo & Offer Badge ==========
            const topHeight = baseSize * 0.07;
            
            // Logo
            if (this.showLogo && this.logoImage && this.logoImage.complete) {
                const logoH = topHeight;
                const logoW = (this.logoImage.width / this.logoImage.height) * logoH;
                
                // White bg
                ctx.fillStyle = '#ffffff';
                this.roundRect(ctx, padding - 5, padding - 5, logoW + 10, logoH + 10, 8);
                ctx.fill();
                
                ctx.drawImage(this.logoImage, padding, padding, logoW, logoH);
            }
            
            // Offer Badge
            if (this.offerText) {
                ctx.font = `bold ${badgeSize}px Arial`;
                const badgeW = ctx.measureText(this.offerText).width + badgeSize * 1.4;
                const badgeH = badgeSize * 1.8;
                const badgeX = W - padding - badgeW;
                const badgeY = padding;
                
                // Shadow
                ctx.shadowColor = 'rgba(0,0,0,0.25)';
                ctx.shadowBlur = 8;
                ctx.shadowOffsetY = 3;
                
                // Red bg
                ctx.fillStyle = '#e53935';
                this.roundRect(ctx, badgeX, badgeY, badgeW, badgeH, badgeH / 2);
                ctx.fill();
                
                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                // Text
                ctx.fillStyle = '#ffffff';
                ctx.font = `bold ${badgeSize}px Arial`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(this.offerText, badgeX + badgeW / 2, badgeY + badgeH / 2);
            }
            
            currentY = padding + topHeight + padding * 1.5;
            
            // ========== MIDDLE SECTION ==========
            const bottomH = this.showContact ? baseSize * 0.09 : padding;
            const middleH = H - currentY - bottomH - padding;
            const centerY = currentY + middleH / 2;
            
            // Calculate content height
            let contentH = 0;
            const imgH = isVert ? baseSize * 0.2 : baseSize * 0.16;
            const gap = padding * 0.7;
            
            if (this.showProductImage && this.productImage) contentH += imgH + gap;
            contentH += headlineSize * 1.4; // headline
            if (this.subheadline || this.productData?.description) contentH += subheadlineSize * 2 + gap;
            if (this.showPrice && this.productData) contentH += newPriceSize * 2.2 + gap;
            if (this.ctaText) contentH += buttonSize * 2.5;
            
            let drawY = centerY - contentH / 2;
            if (drawY < currentY) drawY = currentY;
            
            // Product Image
            if (this.showProductImage && this.productImage && this.productImage.complete) {
                const maxImgH = imgH;
                const maxImgW = W * 0.5;
                const imgRatio = this.productImage.width / this.productImage.height;
                let iW, iH;
                
                if (imgRatio > maxImgW / maxImgH) {
                    iW = maxImgW;
                    iH = iW / imgRatio;
                } else {
                    iH = maxImgH;
                    iW = iH * imgRatio;
                }
                
                const imgX = (W - iW) / 2;
                
                // Shadow
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 20;
                ctx.shadowOffsetY = 8;
                
                // Border
                ctx.fillStyle = 'rgba(255,255,255,0.2)';
                this.roundRect(ctx, imgX - 4, drawY - 4, iW + 8, iH + 8, 14);
                ctx.fill();
                
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                // Image
                ctx.save();
                this.roundRect(ctx, imgX, drawY, iW, iH, 10);
                ctx.clip();
                ctx.drawImage(this.productImage, imgX, drawY, iW, iH);
                ctx.restore();
                
                drawY += iH + gap * 1.2;
            }
            
            // Headline
            ctx.font = `bold ${headlineSize}px Arial`;
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.shadowColor = 'rgba(0,0,0,0.4)';
            ctx.shadowBlur = 6;
            ctx.shadowOffsetY = 2;
            
            const headText = this.headline || this.productData?.name || 'Your Headline';
            const headLines = this.wrapText(ctx, headText, W - padding * 4);
            headLines.forEach((line, i) => {
                ctx.fillText(line, W / 2, drawY + i * headlineSize * 1.2);
            });
            drawY += headLines.length * headlineSize * 1.2 + gap * 0.8;
            
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetY = 0;
            
            // Subheadline
            const subText = this.subheadline || (this.productData?.description || '').substring(0, 50);
            if (subText) {
                ctx.font = `${subheadlineSize}px Arial`;
                ctx.fillStyle = 'rgba(255,255,255,0.85)';
                ctx.shadowColor = 'rgba(0,0,0,0.2)';
                ctx.shadowBlur = 3;
                
                const subLines = this.wrapText(ctx, subText, W - padding * 5);
                subLines.forEach((line, i) => {
                    ctx.fillText(line, W / 2, drawY + i * subheadlineSize * 1.4);
                });
                drawY += subLines.length * subheadlineSize * 1.4 + gap;
                
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
            }
            
            // ========== PRICE SECTION - FIXED ==========
            if (this.showPrice && this.productData) {
                const currentPrice = parseFloat(this.productData.discount_price || this.productData.price);
                const originalPrice = parseFloat(this.productData.price);
                const hasDiscount = this.productData.discount_price && currentPrice !== originalPrice;
                
                // Price box background
                const boxW = W * 0.52;
                const boxH = newPriceSize * 2.2;
                const boxX = (W - boxW) / 2;
                
                ctx.fillStyle = 'rgba(255,255,255,0.15)';
                this.roundRect(ctx, boxX, drawY, boxW, boxH, boxH / 2);
                ctx.fill();
                
                ctx.textBaseline = 'middle';
                const boxCenterY = drawY + boxH / 2;
                
                if (hasDiscount) {
                    // Side by side: OLD PRICE (left) | NEW PRICE (right)
                    const oldText = '₹' + Math.round(originalPrice);
                    const newText = '₹' + Math.round(currentPrice);
                    
                    // Measure widths
                    ctx.font = `${oldPriceSize}px Arial`;
                    const oldW = ctx.measureText(oldText).width;
                    
                    ctx.font = `bold ${newPriceSize}px Arial`;
                    const newW = ctx.measureText(newText).width;
                    
                    const totalW = oldW + 25 + newW; // 25px gap between prices
                    const startX = (W - totalW) / 2;
                    
                    // Draw OLD price (left, with strikethrough)
                    ctx.font = `${oldPriceSize}px Arial`;
                    ctx.fillStyle = 'rgba(255,255,255,0.5)';
                    ctx.textAlign = 'left';
                    ctx.fillText(oldText, startX, boxCenterY);
                    
                    // Strikethrough line
                    ctx.strokeStyle = 'rgba(255,255,255,0.6)';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(startX - 3, boxCenterY);
                    ctx.lineTo(startX + oldW + 3, boxCenterY);
                    ctx.stroke();
                    
                    // Draw NEW price (right, bold)
                    ctx.font = `bold ${newPriceSize}px Arial`;
                    ctx.fillStyle = '#ffffff';
                    ctx.textAlign = 'left';
                    ctx.fillText(newText, startX + oldW + 25, boxCenterY);
                    
                } else {
                    // Single price centered
                    ctx.font = `bold ${newPriceSize}px Arial`;
                    ctx.fillStyle = '#ffffff';
                    ctx.textAlign = 'center';
                    ctx.fillText('₹' + Math.round(currentPrice), W / 2, boxCenterY);
                }
                
                drawY += boxH;
                
                // Weight display
                if (this.productData.weight_display) {
                    ctx.font = `${weightSize}px Arial`;
                    ctx.fillStyle = 'rgba(255,255,255,0.65)';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'top';
                    ctx.fillText(this.productData.weight_display, W / 2, drawY + 6);
                    drawY += weightSize + 12;
                }
                
                drawY += gap;
            }
            
            // CTA Button
            if (this.ctaText) {
                ctx.font = `bold ${buttonSize}px Arial`;
                const btnText = this.ctaText + '  →';
                const btnW = ctx.measureText(btnText).width + buttonSize * 2.5;
                const btnH = buttonSize * 2.3;
                const btnX = (W - btnW) / 2;
                
                // Shadow
                ctx.shadowColor = 'rgba(0,0,0,0.25)';
                ctx.shadowBlur = 12;
                ctx.shadowOffsetY = 5;
                
                // Button bg
                ctx.fillStyle = '#ffffff';
                this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH / 2);
                ctx.fill();
                
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                // Button text
                ctx.fillStyle = theme.primary;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(btnText, W / 2, drawY + btnH / 2);
            }
            
            // ========== BOTTOM SECTION: Contact ==========
            if (this.showContact && this.businessPhone) {
                const contactBoxH = bottomH * 0.75;
                const contactBoxW = W * 0.45;
                const contactX = padding;
                const contactY = H - padding - contactBoxH;
                
                ctx.fillStyle = 'rgba(0,0,0,0.28)';
                this.roundRect(ctx, contactX, contactY, contactBoxW, contactBoxH, 10);
                ctx.fill();
                
                ctx.font = `${contactSize}px Arial`;
                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                
                ctx.fillText('📞  ' + this.businessPhone, contactX + 12, contactY + contactBoxH * 0.35);
                ctx.fillText('🌐  ' + this.businessWebsite, contactX + 12, contactY + contactBoxH * 0.68);
            }
        },
        
        drawBackground(ctx, W, H, theme) {
            const p = theme.primary;
            const s = theme.secondary;
            let grad;
            
            switch(this.selectedPattern) {
                case 'radial':
                    grad = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, Math.max(W, H) * 0.7);
                    grad.addColorStop(0, s);
                    grad.addColorStop(1, p);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    // Decorative circles
                    ctx.fillStyle = s + '45';
                    ctx.beginPath();
                    ctx.arc(W * 0.85, H * 0.1, W * 0.15, 0, Math.PI * 2);
                    ctx.fill();
                    
                    ctx.fillStyle = s + '30';
                    ctx.beginPath();
                    ctx.arc(W * 0.1, H * 0.9, W * 0.12, 0, Math.PI * 2);
                    ctx.fill();
                    break;
                    
                case 'circles':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    [{x:0.08,y:0.12,r:0.1},{x:0.92,y:0.08,r:0.14},{x:0.88,y:0.88,r:0.18},{x:0.05,y:0.82,r:0.08}].forEach(c => {
                        ctx.fillStyle = 'rgba(255,255,255,0.1)';
                        ctx.beginPath();
                        ctx.arc(W * c.x, H * c.y, Math.min(W, H) * c.r, 0, Math.PI * 2);
                        ctx.fill();
                    });
                    break;
                    
                case 'dots':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    ctx.fillStyle = 'rgba(255,255,255,0.08)';
                    const sp = Math.min(W, H) * 0.04;
                    for (let x = sp; x < W; x += sp) {
                        for (let y = sp; y < H; y += sp) {
                            ctx.beginPath();
                            ctx.arc(x, y, sp * 0.1, 0, Math.PI * 2);
                            ctx.fill();
                        }
                    }
                    break;
                    
                case 'diagonal':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    ctx.strokeStyle = 'rgba(255,255,255,0.06)';
                    ctx.lineWidth = 2;
                    const ls = Math.min(W, H) * 0.03;
                    for (let i = -H; i < W + H; i += ls) {
                        ctx.beginPath();
                        ctx.moveTo(i, 0);
                        ctx.lineTo(i + H, H);
                        ctx.stroke();
                    }
                    break;
                    
                default:
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
            }
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
            
            words.forEach(word => {
                const test = line + (line ? ' ' : '') + word;
                if (ctx.measureText(test).width > maxW && line) {
                    lines.push(line);
                    line = word;
                } else {
                    line = test;
                }
            });
            
            if (line) lines.push(line);
            return lines.length ? lines : [text];
        },
        
        downloadBanner(format) {
            const canvas = document.getElementById('bannerCanvas');
            const link = document.createElement('a');
            link.download = `banner_${this.selectedSize}_${Date.now()}.${format}`;
            link.href = format === 'png' ? canvas.toDataURL('image/png', 1.0) : canvas.toDataURL('image/jpeg', 0.95);
            link.click();
        }
    };
}
</script>
@endsection
