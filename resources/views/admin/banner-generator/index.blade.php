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
                                :style="getPatternPreviewStyle(key)">
                            <span x-text="pattern.label" class="drop-shadow"></span>
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
                        <input type="text" x-model="headline" placeholder="Enter headline..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subheadline (short)</label>
                        <input type="text" x-model="subheadline" placeholder="Enter subheadline..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Offer Badge (e.g., "50% OFF")</label>
                        <input type="text" x-model="offerText" placeholder="50% OFF" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" x-model="ctaText" placeholder="Shop Now" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
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
                        <input type="checkbox" x-model="showProductImage" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Product Image</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showPrice" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Price</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showLogo" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Show Logo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showContact" class="rounded text-green-600 focus:ring-green-500">
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
                <button type="button" x-show="customBackground" @click="customBackground = null" class="mt-2 text-xs text-red-600 hover:text-red-700">
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
                        <button type="button" @click="downloadBanner('png')" :disabled="isDownloading" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-2 disabled:opacity-50">
                            <i class="fas" :class="isDownloading ? 'fa-spinner fa-spin' : 'fa-download'"></i> Download PNG
                        </button>
                        <button type="button" @click="downloadBanner('jpg')" :disabled="isDownloading" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-2 disabled:opacity-50">
                            <i class="fas" :class="isDownloading ? 'fa-spinner fa-spin' : 'fa-download'"></i> Download JPG
                        </button>
                    </div>
                </div>
                
                <!-- Preview Container -->
                <div class="flex justify-center items-center bg-gray-200 rounded-lg p-6 min-h-[450px] overflow-auto">
                    <!-- Banner Preview -->
                    <div id="banner-preview" :style="getPreviewContainerStyle()">
                        
                        <!-- Background SVG -->
                        <svg style="position:absolute;top:0;left:0;width:100%;height:100%;" preserveAspectRatio="none" x-html="getSvgPattern()"></svg>
                        
                        <!-- Custom Background -->
                        <template x-if="customBackground">
                            <div style="position:absolute;top:0;left:0;width:100%;height:100%;">
                                <div :style="'position:absolute;top:0;left:0;width:100%;height:100%;background-image:url(' + customBackground + ');background-size:cover;background-position:center;'"></div>
                                <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);"></div>
                            </div>
                        </template>
                        
                        <!-- Main Content Container -->
                        <div :style="'position:relative;width:100%;height:100%;display:flex;flex-direction:column;padding:' + getPadding() + 'px;box-sizing:border-box;'">
                            
                            <!-- TOP ROW: Logo + Offer Badge -->
                            <div :style="'display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:' + getSpacing() + 'px;'">
                                <!-- Logo -->
                                <template x-if="showLogo && logoUrl">
                                    <div :style="'background:#fff;border-radius:' + (getScale() * 10) + 'px;padding:' + (getScale() * 8) + 'px;box-shadow:0 4px 15px rgba(0,0,0,0.2);'">
                                        <img :src="logoUrl" :style="'height:' + (getScale() * 40) + 'px;display:block;'" crossorigin="anonymous">
                                    </div>
                                </template>
                                <template x-if="!showLogo || !logoUrl">
                                    <div></div>
                                </template>
                                
                                <!-- Offer Badge -->
                                <template x-if="offerText">
                                    <div :style="'background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;padding:' + (getScale() * 10) + 'px ' + (getScale() * 20) + 'px;border-radius:50px;font-family:Arial,sans-serif;font-weight:800;font-size:' + (getScale() * 18) + 'px;box-shadow:0 4px 15px rgba(239,68,68,0.4);transform:rotate(2deg);'" x-text="offerText"></div>
                                </template>
                            </div>
                            
                            <!-- MIDDLE: Main Content -->
                            <div :style="'flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;'">
                                
                                <!-- Product Image -->
                                <template x-if="showProductImage && productData?.image_url">
                                    <div :style="'margin-bottom:' + (getScale() * 15) + 'px;'">
                                        <img :src="productData.image_url" 
                                             :style="'max-height:' + (getScale() * (isVertical() ? 160 : 80)) + 'px;max-width:80%;object-fit:contain;border-radius:' + (getScale() * 12) + 'px;box-shadow:0 8px 30px rgba(0,0,0,0.3);border:3px solid rgba(255,255,255,0.3);'"
                                             crossorigin="anonymous">
                                    </div>
                                </template>
                                
                                <!-- Headline -->
                                <div :style="'font-family:Arial,Helvetica,sans-serif;font-weight:800;color:#fff;font-size:' + (getScale() * (isVertical() ? 32 : 24)) + 'px;line-height:1.2;margin-bottom:' + (getScale() * 10) + 'px;text-shadow:2px 2px 6px rgba(0,0,0,0.4);max-width:90%;'"
                                     x-text="headline || productData?.name || 'Your Headline'"></div>
                                
                                <!-- Subheadline -->
                                <template x-if="subheadline || productData?.description">
                                    <div :style="'font-family:Arial,Helvetica,sans-serif;font-weight:400;color:rgba(255,255,255,0.9);font-size:' + (getScale() * (isVertical() ? 16 : 12)) + 'px;line-height:1.4;margin-bottom:' + (getScale() * 15) + 'px;text-shadow:1px 1px 3px rgba(0,0,0,0.3);max-width:85%;'"
                                         x-text="(subheadline || productData?.description || '').substring(0, 100)"></div>
                                </template>
                                
                                <!-- Price -->
                                <template x-if="showPrice && productData">
                                    <div :style="'background:rgba(255,255,255,0.2);border-radius:50px;padding:' + (getScale() * 10) + 'px ' + (getScale() * 25) + 'px;margin-bottom:' + (getScale() * 15) + 'px;'">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:10px;">
                                            <template x-if="productData.discount_price && productData.discount_price != productData.price">
                                                <span :style="'font-family:Arial,sans-serif;color:rgba(255,255,255,0.6);text-decoration:line-through;font-size:' + (getScale() * 16) + 'px;'" x-text="'₹' + formatPrice(productData.price)"></span>
                                            </template>
                                            <span :style="'font-family:Arial,sans-serif;font-weight:800;color:#fff;font-size:' + (getScale() * (isVertical() ? 32 : 24)) + 'px;'" x-text="'₹' + formatPrice(productData.discount_price || productData.price)"></span>
                                        </div>
                                        <template x-if="productData.weight_display">
                                            <div :style="'font-family:Arial,sans-serif;color:rgba(255,255,255,0.8);font-size:' + (getScale() * 11) + 'px;text-align:center;margin-top:4px;'" x-text="productData.weight_display"></div>
                                        </template>
                                    </div>
                                </template>
                                
                                <!-- CTA Button -->
                                <template x-if="ctaText">
                                    <div :style="'background:#fff;color:' + colorThemes[selectedTheme]?.primary + ';padding:' + (getScale() * 12) + 'px ' + (getScale() * 35) + 'px;border-radius:50px;font-family:Arial,sans-serif;font-weight:700;font-size:' + (getScale() * 16) + 'px;box-shadow:0 6px 20px rgba(0,0,0,0.25);display:inline-flex;align-items:center;gap:8px;'">
                                        <span x-text="ctaText"></span>
                                        <span>→</span>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- BOTTOM ROW: Contact Info -->
                            <template x-if="showContact && businessPhone">
                                <div :style="'display:flex;justify-content:flex-start;margin-top:' + getSpacing() + 'px;'">
                                    <div :style="'background:rgba(0,0,0,0.3);border-radius:' + (getScale() * 10) + 'px;padding:' + (getScale() * 10) + 'px ' + (getScale() * 15) + 'px;'">
                                        <div :style="'font-family:Arial,sans-serif;color:#fff;font-size:' + (getScale() * 13) + 'px;display:flex;align-items:center;gap:8px;'">
                                            <span>📞</span>
                                            <span x-text="businessPhone"></span>
                                        </div>
                                        <div :style="'font-family:Arial,sans-serif;color:#fff;font-size:' + (getScale() * 12) + 'px;display:flex;align-items:center;gap:8px;margin-top:4px;'">
                                            <span>🌐</span>
                                            <span x-text="businessWebsite"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
function bannerGenerator() {
    return {
        bannerSizes: @json($bannerSizes),
        colorThemes: @json($colorThemes),
        businessName: @json($businessName),
        businessPhone: @json($businessPhone),
        businessWebsite: window.location.origin.replace('http://', '').replace('https://', ''),
        logoUrl: @json($logoUrl),
        
        backgroundPatterns: {
            'solid': { label: 'Solid' },
            'radial': { label: 'Radial' },
            'circles': { label: 'Circles' },
            'dots': { label: 'Dots' },
            'waves': { label: 'Waves' },
            'geometric': { label: 'Geo' },
            'diagonal': { label: 'Lines' },
            'spotlight': { label: 'Spot' },
        },
        
        selectedSize: 'instagram_post',
        selectedTheme: 'green_fresh',
        selectedPattern: 'radial',
        selectedProduct: '',
        selectedTemplate: 'product_showcase',
        
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
        isDownloading: false,
        
        setSize(size) { this.selectedSize = size; },
        setTheme(theme) { this.selectedTheme = theme; },
        setPattern(pattern) { this.selectedPattern = pattern; },
        
        isVertical() {
            const size = this.bannerSizes[this.selectedSize];
            return size && size.height > size.width;
        },
        
        getScale() {
            const size = this.bannerSizes[this.selectedSize];
            const previewWidth = Math.min(500, size.width);
            return previewWidth / 500;
        },
        
        getPadding() {
            return this.getScale() * (this.isVertical() ? 25 : 20);
        },
        
        getSpacing() {
            return this.getScale() * 10;
        },
        
        formatPrice(price) {
            if (!price) return '0';
            return parseFloat(price).toFixed(2);
        },
        
        clearProduct() {
            this.selectedProduct = '';
            this.productData = null;
            this.headline = '';
            this.subheadline = '';
            this.offerText = '';
        },
        
        getPatternPreviewStyle(key) {
            const theme = this.colorThemes[this.selectedTheme];
            return { background: `linear-gradient(135deg, ${theme?.primary} 0%, ${theme?.secondary} 100%)` };
        },
        
        getSvgPattern() {
            const theme = this.colorThemes[this.selectedTheme];
            const p = theme?.primary || '#16a34a';
            const s = theme?.secondary || '#22c55e';
            
            const patterns = {
                'solid': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/>`,
                
                'radial': `<defs><radialGradient id="g1" cx="50%" cy="50%" r="70%"><stop offset="0%" stop-color="${s}"/><stop offset="100%" stop-color="${p}"/></radialGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/><circle cx="85%" cy="15%" r="20%" fill="${s}" opacity="0.3"/><circle cx="15%" cy="85%" r="15%" fill="${s}" opacity="0.2"/>`,
                
                'circles': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/><circle cx="10%" cy="20%" r="15%" fill="white" opacity="0.1"/><circle cx="90%" cy="10%" r="20%" fill="white" opacity="0.08"/><circle cx="80%" cy="80%" r="25%" fill="white" opacity="0.1"/><circle cx="5%" cy="75%" r="12%" fill="white" opacity="0.12"/>`,
                
                'dots': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient><pattern id="dots" width="30" height="30" patternUnits="userSpaceOnUse"><circle cx="15" cy="15" r="3" fill="white" opacity="0.15"/></pattern></defs><rect width="100%" height="100%" fill="url(#g1)"/><rect width="100%" height="100%" fill="url(#dots)"/>`,
                
                'waves': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/><ellipse cx="50%" cy="110%" rx="80%" ry="30%" fill="white" opacity="0.08"/><ellipse cx="50%" cy="120%" rx="70%" ry="25%" fill="white" opacity="0.05"/>`,
                
                'geometric': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/><polygon points="0,0 30%,0 0,30%" fill="white" opacity="0.1"/><polygon points="100%,100% 70%,100% 100%,70%" fill="white" opacity="0.1"/>`,
                
                'diagonal': `<defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="${p}"/><stop offset="100%" stop-color="${s}"/></linearGradient><pattern id="lines" width="20" height="20" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><line x1="0" y1="0" x2="0" y2="20" stroke="white" stroke-width="1" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g1)"/><rect width="100%" height="100%" fill="url(#lines)"/>`,
                
                'spotlight': `<defs><radialGradient id="g1" cx="30%" cy="30%" r="80%"><stop offset="0%" stop-color="${s}"/><stop offset="100%" stop-color="${p}"/></radialGradient></defs><rect width="100%" height="100%" fill="url(#g1)"/><circle cx="25%" cy="25%" r="30%" fill="white" opacity="0.1"/>`,
            };
            
            return patterns[this.selectedPattern] || patterns['solid'];
        },
        
        async loadProductDetails() {
            if (!this.selectedProduct) {
                this.productData = null;
                return;
            }
            
            try {
                const response = await fetch(`{{ route('admin.banner-generator.product-details') }}?product_id=${this.selectedProduct}`);
                this.productData = await response.json();
                
                this.headline = this.productData.name || '';
                this.subheadline = (this.productData.description || '').substring(0, 80);
                
                if (this.productData.discount_percentage > 0) {
                    this.offerText = Math.round(this.productData.discount_percentage) + '% OFF';
                } else {
                    this.offerText = '';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        handleBackgroundUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.customBackground = e.target.result; };
                reader.readAsDataURL(file);
            }
        },
        
        getPreviewContainerStyle() {
            const size = this.bannerSizes[this.selectedSize];
            const maxW = 500, maxH = 600;
            const scale = Math.min(maxW / size.width, maxH / size.height, 1);
            
            return {
                width: (size.width * scale) + 'px',
                height: (size.height * scale) + 'px',
                position: 'relative',
                overflow: 'hidden',
                boxShadow: '0 10px 40px rgba(0,0,0,0.3)',
                borderRadius: '8px',
                backgroundColor: '#000'
            };
        },
        
        async downloadBanner(format) {
            if (this.isDownloading) return;
            this.isDownloading = true;
            
            const element = document.getElementById('banner-preview');
            const size = this.bannerSizes[this.selectedSize];
            const scale = size.width / element.offsetWidth;
            
            try {
                const canvas = await html2canvas(element, {
                    scale: scale,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    logging: false,
                });
                
                const link = document.createElement('a');
                link.download = `banner_${this.selectedSize}_${Date.now()}.${format}`;
                link.href = canvas.toDataURL(format === 'png' ? 'image/png' : 'image/jpeg', 0.95);
                link.click();
            } catch (err) {
                console.error('Download error:', err);
                alert('Error downloading. Please try again.');
            } finally {
                this.isDownloading = false;
            }
        }
    };
}
</script>
@endsection
