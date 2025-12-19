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
                <button type="button" x-show="customBackground" @click="customBackground = null; renderCanvas()" class="mt-2 text-xs text-red-600 hover:text-red-700">
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
            // Load logo image
            if (this.logoUrl) {
                this.logoImage = new Image();
                this.logoImage.crossOrigin = 'anonymous';
                this.logoImage.onload = () => this.renderCanvas();
                this.logoImage.src = this.logoUrl;
            }
            
            // Initial render
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
        
        formatPrice(price) {
            if (!price) return '0.00';
            return parseFloat(price).toFixed(2);
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
                this.subheadline = (this.productData.description || '').substring(0, 60);
                
                if (this.productData.discount_percentage > 0) {
                    this.offerText = Math.round(this.productData.discount_percentage) + '% OFF';
                } else {
                    this.offerText = '';
                }
                
                // Load product image
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
            
            // Set canvas size
            canvas.width = W;
            canvas.height = H;
            
            // Scale for display
            const maxDisplayWidth = 500;
            const maxDisplayHeight = 600;
            const displayScale = Math.min(maxDisplayWidth / W, maxDisplayHeight / H, 1);
            canvas.style.width = (W * displayScale) + 'px';
            canvas.style.height = (H * displayScale) + 'px';
            
            // Draw background
            this.drawBackground(ctx, W, H, theme);
            
            // Draw custom background if set
            if (this.customBgImage) {
                ctx.drawImage(this.customBgImage, 0, 0, W, H);
                ctx.fillStyle = 'rgba(0,0,0,0.4)';
                ctx.fillRect(0, 0, W, H);
            }
            
            // Calculate padding
            const padding = Math.min(W, H) * 0.05;
            const isVert = this.isVertical();
            
            // Font sizes based on canvas size
            const baseSize = Math.min(W, H);
            const headlineSize = baseSize * (isVert ? 0.07 : 0.08);
            const subheadlineSize = baseSize * (isVert ? 0.035 : 0.04);
            const priceSize = baseSize * (isVert ? 0.065 : 0.07);
            const oldPriceSize = baseSize * (isVert ? 0.035 : 0.04);
            const buttonSize = baseSize * (isVert ? 0.04 : 0.045);
            const badgeSize = baseSize * (isVert ? 0.04 : 0.045);
            const contactSize = baseSize * (isVert ? 0.028 : 0.032);
            
            let currentY = padding;
            
            // TOP SECTION: Logo and Offer Badge
            const topSectionHeight = baseSize * 0.1;
            
            // Draw Logo
            if (this.showLogo && this.logoImage && this.logoImage.complete) {
                const logoHeight = topSectionHeight * 0.8;
                const logoWidth = (this.logoImage.width / this.logoImage.height) * logoHeight;
                const logoX = padding;
                const logoY = padding;
                
                // White background for logo
                ctx.fillStyle = '#ffffff';
                this.roundRect(ctx, logoX - 8, logoY - 8, logoWidth + 16, logoHeight + 16, 12);
                ctx.fill();
                
                ctx.drawImage(this.logoImage, logoX, logoY, logoWidth, logoHeight);
            }
            
            // Draw Offer Badge
            if (this.offerText) {
                ctx.font = `800 ${badgeSize}px Arial, sans-serif`;
                const badgeText = this.offerText;
                const badgeMetrics = ctx.measureText(badgeText);
                const badgePadX = badgeSize * 0.8;
                const badgePadY = badgeSize * 0.5;
                const badgeW = badgeMetrics.width + badgePadX * 2;
                const badgeH = badgeSize + badgePadY * 2;
                const badgeX = W - padding - badgeW;
                const badgeY = padding;
                
                // Red gradient badge
                const badgeGrad = ctx.createLinearGradient(badgeX, badgeY, badgeX + badgeW, badgeY + badgeH);
                badgeGrad.addColorStop(0, '#ef4444');
                badgeGrad.addColorStop(1, '#dc2626');
                ctx.fillStyle = badgeGrad;
                this.roundRect(ctx, badgeX, badgeY, badgeW, badgeH, badgeH / 2);
                ctx.fill();
                
                // Badge text
                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(badgeText, badgeX + badgeW / 2, badgeY + badgeH / 2);
            }
            
            currentY = padding + topSectionHeight + padding;
            
            // MIDDLE SECTION
            const bottomSectionHeight = this.showContact ? baseSize * 0.12 : 0;
            const middleSectionHeight = H - currentY - bottomSectionHeight - padding * 2;
            const middleCenterY = currentY + middleSectionHeight / 2;
            
            // Calculate total content height to center it
            let contentHeight = 0;
            const productImgHeight = isVert ? baseSize * 0.25 : baseSize * 0.2;
            
            if (this.showProductImage && this.productImage) contentHeight += productImgHeight + padding;
            contentHeight += headlineSize * 1.5; // headline
            if (this.subheadline || this.productData?.description) contentHeight += subheadlineSize * 2 + padding;
            if (this.showPrice && this.productData) contentHeight += priceSize * 2 + padding;
            if (this.ctaText) contentHeight += buttonSize * 2.5;
            
            let drawY = middleCenterY - contentHeight / 2;
            
            // Draw Product Image
            if (this.showProductImage && this.productImage && this.productImage.complete) {
                const imgMaxH = productImgHeight;
                const imgMaxW = W * 0.6;
                const imgRatio = this.productImage.width / this.productImage.height;
                let imgW, imgH;
                
                if (imgRatio > imgMaxW / imgMaxH) {
                    imgW = imgMaxW;
                    imgH = imgW / imgRatio;
                } else {
                    imgH = imgMaxH;
                    imgW = imgH * imgRatio;
                }
                
                const imgX = (W - imgW) / 2;
                
                // Shadow
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 20;
                ctx.shadowOffsetY = 10;
                
                // White border
                ctx.fillStyle = 'rgba(255,255,255,0.3)';
                this.roundRect(ctx, imgX - 4, drawY - 4, imgW + 8, imgH + 8, 16);
                ctx.fill();
                
                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                // Clip and draw image
                ctx.save();
                this.roundRect(ctx, imgX, drawY, imgW, imgH, 12);
                ctx.clip();
                ctx.drawImage(this.productImage, imgX, drawY, imgW, imgH);
                ctx.restore();
                
                drawY += imgH + padding * 1.5;
            }
            
            // Draw Headline
            const headlineText = this.headline || this.productData?.name || 'Your Headline';
            ctx.font = `800 ${headlineSize}px Arial, sans-serif`;
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.shadowColor = 'rgba(0,0,0,0.4)';
            ctx.shadowBlur = 6;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            
            // Word wrap headline
            const headlineLines = this.wrapText(ctx, headlineText, W - padding * 4);
            headlineLines.forEach((line, i) => {
                ctx.fillText(line, W / 2, drawY + i * headlineSize * 1.2);
            });
            drawY += headlineLines.length * headlineSize * 1.2 + padding * 0.8;
            
            // Reset shadow
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
            
            // Draw Subheadline
            const subText = this.subheadline || (this.productData?.description || '').substring(0, 60);
            if (subText) {
                ctx.font = `400 ${subheadlineSize}px Arial, sans-serif`;
                ctx.fillStyle = 'rgba(255,255,255,0.9)';
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 4;
                
                const subLines = this.wrapText(ctx, subText, W - padding * 4);
                subLines.forEach((line, i) => {
                    ctx.fillText(line, W / 2, drawY + i * subheadlineSize * 1.4);
                });
                drawY += subLines.length * subheadlineSize * 1.4 + padding;
                
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
            }
            
            // Draw Price
            if (this.showPrice && this.productData) {
                const currentPrice = this.formatPrice(this.productData.discount_price || this.productData.price);
                const originalPrice = this.productData.discount_price ? this.formatPrice(this.productData.price) : null;
                
                // Price background
                ctx.fillStyle = 'rgba(255,255,255,0.15)';
                const priceBoxW = W * 0.6;
                const priceBoxH = priceSize * 2.2;
                const priceBoxX = (W - priceBoxW) / 2;
                this.roundRect(ctx, priceBoxX, drawY, priceBoxW, priceBoxH, priceBoxH / 2);
                ctx.fill();
                
                const priceCenterY = drawY + priceBoxH / 2;
                
                // Draw prices
                if (originalPrice && originalPrice !== currentPrice) {
                    // Original price (strikethrough)
                    ctx.font = `400 ${oldPriceSize}px Arial, sans-serif`;
                    ctx.fillStyle = 'rgba(255,255,255,0.5)';
                    const oldPriceText = '₹' + originalPrice;
                    const oldPriceWidth = ctx.measureText(oldPriceText).width;
                    const oldPriceX = W / 2 - oldPriceWidth / 2 - priceSize * 1.5;
                    ctx.fillText(oldPriceText, W / 2 - priceSize * 0.8, priceCenterY - oldPriceSize * 0.3);
                    
                    // Strikethrough line
                    ctx.strokeStyle = 'rgba(255,255,255,0.5)';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(W / 2 - priceSize * 0.8 - oldPriceWidth / 2 - 5, priceCenterY - oldPriceSize * 0.3);
                    ctx.lineTo(W / 2 - priceSize * 0.8 + oldPriceWidth / 2 + 5, priceCenterY - oldPriceSize * 0.3);
                    ctx.stroke();
                    
                    // Current price
                    ctx.font = `800 ${priceSize}px Arial, sans-serif`;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText('₹' + currentPrice, W / 2 + priceSize * 0.5, priceCenterY);
                } else {
                    // Just current price
                    ctx.font = `800 ${priceSize}px Arial, sans-serif`;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText('₹' + currentPrice, W / 2, priceCenterY);
                }
                
                // Weight display
                if (this.productData.weight_display) {
                    ctx.font = `400 ${contactSize}px Arial, sans-serif`;
                    ctx.fillStyle = 'rgba(255,255,255,0.7)';
                    ctx.fillText(this.productData.weight_display, W / 2, drawY + priceBoxH + contactSize);
                    drawY += priceBoxH + contactSize * 1.5 + padding;
                } else {
                    drawY += priceBoxH + padding;
                }
            }
            
            // Draw CTA Button
            if (this.ctaText) {
                ctx.font = `700 ${buttonSize}px Arial, sans-serif`;
                const btnText = this.ctaText + '  →';
                const btnMetrics = ctx.measureText(btnText);
                const btnPadX = buttonSize * 1.5;
                const btnPadY = buttonSize * 0.7;
                const btnW = btnMetrics.width + btnPadX * 2;
                const btnH = buttonSize + btnPadY * 2;
                const btnX = (W - btnW) / 2;
                
                // Button shadow
                ctx.shadowColor = 'rgba(0,0,0,0.25)';
                ctx.shadowBlur = 15;
                ctx.shadowOffsetY = 6;
                
                // Button background
                ctx.fillStyle = '#ffffff';
                this.roundRect(ctx, btnX, drawY, btnW, btnH, btnH / 2);
                ctx.fill();
                
                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;
                
                // Button text
                ctx.fillStyle = theme.primary;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(btnText, W / 2, drawY + btnH / 2);
            }
            
            // BOTTOM SECTION: Contact Info
            if (this.showContact && this.businessPhone) {
                const contactY = H - padding - bottomSectionHeight;
                const contactBoxW = W * 0.5;
                const contactBoxH = bottomSectionHeight * 0.8;
                
                // Contact background
                ctx.fillStyle = 'rgba(0,0,0,0.25)';
                this.roundRect(ctx, padding, contactY, contactBoxW, contactBoxH, 12);
                ctx.fill();
                
                ctx.font = `400 ${contactSize}px Arial, sans-serif`;
                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                
                // Phone
                ctx.fillText('📞  ' + this.businessPhone, padding + 15, contactY + contactBoxH * 0.35);
                
                // Website
                ctx.fillText('🌐  ' + this.businessWebsite, padding + 15, contactY + contactBoxH * 0.7);
            }
        },
        
        drawBackground(ctx, W, H, theme) {
            const p = theme.primary;
            const s = theme.secondary;
            
            // Create gradient
            let grad;
            switch(this.selectedPattern) {
                case 'radial':
                    grad = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, Math.max(W, H) * 0.7);
                    grad.addColorStop(0, s);
                    grad.addColorStop(1, p);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    // Decorative circles
                    ctx.fillStyle = s + '40';
                    ctx.beginPath();
                    ctx.arc(W * 0.85, H * 0.15, W * 0.2, 0, Math.PI * 2);
                    ctx.fill();
                    
                    ctx.fillStyle = s + '30';
                    ctx.beginPath();
                    ctx.arc(W * 0.15, H * 0.85, W * 0.15, 0, Math.PI * 2);
                    ctx.fill();
                    break;
                    
                case 'circles':
                    grad = ctx.createLinearGradient(0, 0, W, H);
                    grad.addColorStop(0, p);
                    grad.addColorStop(1, s);
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, 0, W, H);
                    
                    // Multiple circles
                    const circles = [
                        {x: 0.1, y: 0.2, r: 0.15},
                        {x: 0.9, y: 0.1, r: 0.2},
                        {x: 0.8, y: 0.8, r: 0.25},
                        {x: 0.05, y: 0.75, r: 0.12},
                        {x: 0.5, y: 0.5, r: 0.08},
                    ];
                    circles.forEach(c => {
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
                    
                    // Dot pattern
                    ctx.fillStyle = 'rgba(255,255,255,0.12)';
                    const dotSpacing = Math.min(W, H) * 0.05;
                    const dotRadius = dotSpacing * 0.15;
                    for (let x = dotSpacing; x < W; x += dotSpacing) {
                        for (let y = dotSpacing; y < H; y += dotSpacing) {
                            ctx.beginPath();
                            ctx.arc(x, y, dotRadius, 0, Math.PI * 2);
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
                    
                    // Diagonal lines
                    ctx.strokeStyle = 'rgba(255,255,255,0.08)';
                    ctx.lineWidth = 2;
                    const lineSpacing = Math.min(W, H) * 0.04;
                    for (let i = -H; i < W + H; i += lineSpacing) {
                        ctx.beginPath();
                        ctx.moveTo(i, 0);
                        ctx.lineTo(i + H, H);
                        ctx.stroke();
                    }
                    break;
                    
                default: // solid
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
        
        wrapText(ctx, text, maxWidth) {
            const words = text.split(' ');
            const lines = [];
            let currentLine = '';
            
            words.forEach(word => {
                const testLine = currentLine + (currentLine ? ' ' : '') + word;
                const metrics = ctx.measureText(testLine);
                
                if (metrics.width > maxWidth && currentLine) {
                    lines.push(currentLine);
                    currentLine = word;
                } else {
                    currentLine = testLine;
                }
            });
            
            if (currentLine) {
                lines.push(currentLine);
            }
            
            return lines.length > 0 ? lines : [text];
        },
        
        downloadBanner(format) {
            const canvas = document.getElementById('bannerCanvas');
            const link = document.createElement('a');
            link.download = `banner_${this.selectedSize}_${Date.now()}.${format}`;
            
            if (format === 'png') {
                link.href = canvas.toDataURL('image/png', 1.0);
            } else {
                link.href = canvas.toDataURL('image/jpeg', 0.95);
            }
            
            link.click();
        }
    };
}
</script>
@endsection
