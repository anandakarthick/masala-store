@extends('layouts.admin')
@section('title', 'Social Media & WhatsApp')
@section('page_title', 'Social Media & WhatsApp Settings')

@section('content')
<!-- Announcement Bar / Marquee Settings -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <i class="fas fa-bullhorn text-orange-500 text-xl mr-2"></i>
        Announcement Bar (Running Text)
    </h3>
    <form action="{{ route('admin.settings.social-media.marquee') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="flex items-center mb-3 cursor-pointer">
                        <input type="checkbox" name="marquee_enabled" value="1" 
                               {{ $marqueeEnabled == '1' ? 'checked' : '' }}
                               class="w-5 h-5 text-orange-600 focus:ring-orange-500 rounded">
                        <span class="ml-2 text-sm font-medium">Enable Announcement Bar</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Announcement Text *</label>
                    <textarea name="marquee_text" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="🎉 Free Shipping on Orders Above ₹500 | 100% Pure & Natural Products | Order Now! 🌿">{{ $marqueeText }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Use emojis to make it attractive! This text scrolls between WhatsApp button and Phone number.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scroll Speed (seconds)</label>
                    <input type="number" name="marquee_speed" value="{{ $marqueeSpeed }}" min="10" max="120"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Time for one complete scroll. Lower = faster. Recommended: 25-40 seconds</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="marquee_bg_color" value="{{ $marqueeBgColor }}"
                               class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" value="{{ $marqueeBgColor }}" readonly
                               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 text-sm"
                               id="marquee-color-text">
                        <div class="flex gap-1">
                            <button type="button" onclick="setMarqueeColor('#15803d')" class="w-8 h-8 rounded bg-green-700 border-2 border-transparent hover:border-gray-400" title="Green"></button>
                            <button type="button" onclick="setMarqueeColor('#dc2626')" class="w-8 h-8 rounded bg-red-600 border-2 border-transparent hover:border-gray-400" title="Red"></button>
                            <button type="button" onclick="setMarqueeColor('#2563eb')" class="w-8 h-8 rounded bg-blue-600 border-2 border-transparent hover:border-gray-400" title="Blue"></button>
                            <button type="button" onclick="setMarqueeColor('#7c3aed')" class="w-8 h-8 rounded bg-purple-600 border-2 border-transparent hover:border-gray-400" title="Purple"></button>
                            <button type="button" onclick="setMarqueeColor('#ea580c')" class="w-8 h-8 rounded bg-orange-600 border-2 border-transparent hover:border-gray-400" title="Orange"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Preview -->
        <div class="mt-4 p-4 bg-gray-100 rounded-lg">
            <p class="text-xs text-gray-600 font-medium mb-2">Preview:</p>
            <div class="text-white text-xs py-1.5 rounded overflow-hidden" id="marquee-preview" style="background-color: {{ $marqueeBgColor }}">
                <div class="whitespace-nowrap animate-pulse">
                    {{ $marqueeText }}
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-save"></i> Save Announcement Settings
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- WhatsApp Settings -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fab fa-whatsapp text-green-500 text-xl mr-2"></i>
                WhatsApp Order Button
            </h3>
            <form action="{{ route('admin.settings.social-media.whatsapp') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center mb-3 cursor-pointer">
                            <input type="checkbox" name="whatsapp_enabled" value="1" 
                                   {{ $whatsappEnabled == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2 text-sm font-medium">Enable WhatsApp Order Button</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number *</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                +91
                            </span>
                            <input type="text" name="whatsapp_number" value="{{ $whatsappNumber }}" required
                                   placeholder="9876543210" maxlength="10"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter 10-digit mobile number without country code</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Message</label>
                        <textarea name="whatsapp_default_message" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Hello! I would like to place an order.">{{ $whatsappMessage }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">This message will be pre-filled when customers click the WhatsApp button</p>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp"></i> Save WhatsApp Settings
                    </button>
                </div>
            </form>
            
            <!-- Preview -->
            @if($whatsappNumber)
                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-xs text-green-700 font-medium mb-2">Preview:</p>
                    <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm">
                        <i class="fab fa-whatsapp"></i> Order via WhatsApp
                    </a>
                </div>
            @endif
        </div>

        <!-- Add Social Media Link -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                Add Social Media Link
            </h3>
            <form action="{{ route('admin.settings.social-media.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform *</label>
                        <select name="platform" id="platform-select" required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="updatePlatformDefaults()">
                            <option value="">Select Platform</option>
                            @foreach($platforms as $key => $platform)
                                <option value="{{ $key }}" data-icon="{{ $platform['icon'] }}" data-color="{{ $platform['color'] }}">
                                    {{ $platform['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                        <input type="text" name="name" id="platform-name" required
                               placeholder="e.g., Follow us on Instagram"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL *</label>
                        <input type="url" name="url" required
                               placeholder="https://facebook.com/yourpage"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" name="icon" id="platform-icon"
                                   placeholder="fab fa-facebook"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" name="color" id="platform-color" value="#4267B2"
                                   class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="text-blue-600 focus:ring-blue-500 rounded">
                            <span class="ml-2 text-sm">Active</span>
                        </label>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600">Sort Order</label>
                            <input type="number" name="sort_order" value="0"
                                   class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Add Link
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Social Media Links List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-share-alt text-blue-500 mr-2"></i>
                    Social Media Links ({{ $socialLinks->count() }})
                </h3>
            </div>
            <div class="p-6">
                @if($socialLinks->count() > 0)
                    <div class="space-y-4">
                        @foreach($socialLinks as $link)
                            <div class="border rounded-lg p-4 {{ $link->is_active ? '' : 'bg-gray-50 opacity-60' }}" 
                                 x-data="{ editing: false }">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <!-- Icon Preview -->
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl"
                                             style="background-color: {{ $link->color ?? '#6B7280' }}">
                                            <i class="{{ $link->icon ?? 'fas fa-link' }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $link->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $link->platform }} • <a href="{{ $link->url }}" target="_blank" class="text-blue-500 hover:underline">{{ Str::limit($link->url, 40) }}</a></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $link->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $link->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="text-xs text-gray-400">#{{ $link->sort_order }}</span>
                                        <button @click="editing = !editing" class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.social-media.destroy', $link) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this link?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 p-1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Edit Form -->
                                <div x-show="editing" x-collapse class="mt-4 pt-4 border-t">
                                    <form action="{{ route('admin.settings.social-media.update', $link) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                                                <select name="platform" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                                    @foreach($platforms as $key => $platform)
                                                        <option value="{{ $key }}" {{ $link->platform === $key ? 'selected' : '' }}>
                                                            {{ $platform['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Display Name</label>
                                                <input type="text" name="name" value="{{ $link->name }}" required
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">URL</label>
                                                <input type="url" name="url" value="{{ $link->url }}" required
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                                                <input type="text" name="icon" value="{{ $link->icon }}"
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                                                <input type="color" name="color" value="{{ $link->color ?? '#6B7280' }}"
                                                       class="w-full h-9 border border-gray-300 rounded cursor-pointer">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                                                <input type="number" name="sort_order" value="{{ $link->sort_order }}"
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            </div>
                                            <div class="flex items-center">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" name="is_active" value="1" {{ $link->is_active ? 'checked' : '' }}
                                                           class="text-blue-600 rounded">
                                                    <span class="ml-2 text-sm">Active</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex gap-2">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                                Save Changes
                                            </button>
                                            <button type="button" @click="editing = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Preview -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Preview (as shown on website)</h4>
                        <div class="flex items-center gap-3">
                            @foreach($socialLinks->where('is_active', true) as $link)
                                <a href="{{ $link->url }}" target="_blank" 
                                   class="w-10 h-10 rounded-full flex items-center justify-center text-white transition transform hover:scale-110"
                                   style="background-color: {{ $link->color ?? '#6B7280' }}"
                                   title="{{ $link->name }}">
                                    <i class="{{ $link->icon ?? 'fas fa-link' }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-share-alt text-3xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-600 mb-2">No Social Links Yet</h4>
                        <p class="text-gray-500 text-sm">Add your social media links to display on the website</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updatePlatformDefaults() {
    const select = document.getElementById('platform-select');
    const option = select.options[select.selectedIndex];
    const nameInput = document.getElementById('platform-name');
    const iconInput = document.getElementById('platform-icon');
    const colorInput = document.getElementById('platform-color');
    
    if (option.value) {
        const platformName = option.text;
        nameInput.value = platformName;
        iconInput.value = option.dataset.icon || '';
        colorInput.value = option.dataset.color || '#6B7280';
    }
}

function setMarqueeColor(color) {
    document.querySelector('input[name="marquee_bg_color"]').value = color;
    document.getElementById('marquee-color-text').value = color;
    document.getElementById('marquee-preview').style.backgroundColor = color;
}

// Update color text when color picker changes
document.querySelector('input[name="marquee_bg_color"]').addEventListener('input', function(e) {
    document.getElementById('marquee-color-text').value = e.target.value;
    document.getElementById('marquee-preview').style.backgroundColor = e.target.value;
});
</script>
@endsection
