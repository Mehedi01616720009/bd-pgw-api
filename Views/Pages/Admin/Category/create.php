<?php

include_once __DIR__ . '/../../../Layouts/Admin/AfterLogin.php';
$categories = $Categories['data'];
$meta = $Categories['meta'];

?>

<div class="max-w-[1520px] 2xl:w-full py-4 mx-5 2xl:mx-auto mb-6">
    <div class="max-w-4xl mx-auto pb-10">

        <?php if ($success = flash('success')): ?>
            <div class="bg-lime-50 text-sm text-lime-600 px-4 py-2 rounded-lg mb-4">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg mb-4">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?= route('categories.store') ?>" class="space-y-8" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Basic Information Card -->
            <div class="border border-dashed rounded-xl p-6">
                <h2 class="text-lg font-bold text-primary mb-6 pb-4 border-b">Basic Information</h2>

                <!-- Category Name -->
                <div class="grid gap-2 mb-6">
                    <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-2">
                        Category Name *
                    </label>
                    <input
                        type="text"
                        id="categoryName"
                        name="name"
                        value="<?= old('name') ?>"
                        placeholder="Enter category name (e.g., Electronics)"
                        class="w-full h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717]  <?= hasError('name') ? 'bg-rose-50' : 'bg-[#f5f5f5]' ?>">
                    <?php if (hasError('name')): ?>
                        <span class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg"><?= errors('name') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Parent Category -->
                <div class="grid gap-2 mb-6">
                    <label for="parentCategory" class="block text-sm font-medium text-gray-700 mb-2">
                        Parent Category
                    </label>
                    <select
                        id="parentCategory"
                        name="parentID"
                        class="w-full h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] bg-[#f5f5f5]">
                        <option value="">Parent</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= old('parentID') === $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="grid gap-2">
                    <label for="categoryStatus" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select
                        id="categoryStatus"
                        name="status"
                        class="w-full h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717]  <?= hasError('status') ? 'bg-rose-50' : 'bg-[#f5f5f5]' ?>">
                        <option value="Active" <?= old('status') === 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= old('status') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <?php if (hasError('status')): ?>
                        <span class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg"><?= errors('status') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Image Upload Card -->
            <div class="border border-dashed rounded-xl p-6">
                <h2 class="text-lg font-bold text-primary mb-6 pb-4 border-b">Category Image</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Image Upload Area -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Upload Image *
                        </label>

                        <div
                            id="dropZone"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition-colors cursor-pointer bg-background/50">
                            <div class="space-y-4">
                                <div class="mx-auto w-16 h-16 bg-background rounded-full flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-primary mb-1">
                                        Drag & drop your image here
                                    </p>
                                    <p class="text-xs text-gray-500 mb-4">
                                        or click to browse
                                    </p>
                                    <input
                                        type="file"
                                        id="imageUpload"
                                        name="image"
                                        accept="image/*"
                                        class="hidden">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('imageUpload').click()"
                                        class="px-6 py-2 bg-white border border-gray-300 text-primary font-medium rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                        <i class="fas fa-folder-open mr-2"></i>
                                        Browse Files
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400">
                                    Supports JPG, PNG, WEBP • Max 5MB
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Image Preview
                        </label>

                        <div
                            id="imagePreviewContainer"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-background image-preview-container"
                            style="height: 300px;">
                            <!-- Default Preview -->
                            <div id="defaultPreview" class="flex flex-col items-center justify-center h-full p-6">
                                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <img src="<?= asset('assets/images/empty.png') ?>" alt="">
                                </div>
                                <p class="text-sm font-medium text-primary mb-1">No image selected</p>
                                <p class="text-xs text-gray-500 text-center">Upload an image to see preview here</p>
                            </div>

                            <!-- Image Preview (hidden by default) -->
                            <div id="imagePreview" class="hidden absolute inset-0">
                                <img
                                    id="previewImage"
                                    class="w-full h-full object-cover"
                                    alt="Category image preview">
                                <!-- Overlay for actions -->
                                <div class="image-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 flex items-center justify-center space-x-4">
                                    <button
                                        type="button"
                                        onclick="replaceImage()"
                                        class="px-4 py-2 bg-white text-primary font-medium rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-sync mr-2"></i>Replace
                                    </button>
                                    <button
                                        type="button"
                                        onclick="removeImage()"
                                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Image Info -->
                        <div id="imageInfo" class="mt-4 space-y-2 hidden">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">File name:</span>
                                <span id="fileName" class="font-medium text-primary truncate ml-2"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">File size:</span>
                                <span id="fileSize" class="font-medium text-primary"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Dimensions:</span>
                                <span id="fileDimensions" class="font-medium text-primary"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Sticky at bottom on mobile) -->
            <div class="fixed bottom-0 left-0 right-0 bg-white p-4 flex justify-center items-center gap-4">
                <a href="<?= route('categories.index') ?>" class="border border-dashed hover:bg-gray-50 text-gray-800 px-5 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>

                <button type="submit" class="bg-gray-200 hover:bg-gray-200/60 text-gray-800 px-5 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload handling
        const imageUpload = document.getElementById('imageUpload');
        const dropZone = document.getElementById('dropZone');
        const defaultPreview = document.getElementById('defaultPreview');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const imageInfo = document.getElementById('imageInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileDimensions = document.getElementById('fileDimensions');

        // Handle file selection via input
        imageUpload.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleImageFile(this.files[0]);
            }
        });

        // Handle drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-primary', 'bg-background');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary', 'bg-background');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary', 'bg-background');

            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleImageFile(e.dataTransfer.files[0]);
            }
        });

        function handleImageFile(file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file (JPG, PNG, WEBP, etc.)');
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            // Read and preview the image
            const reader = new FileReader();
            reader.onload = function(e) {
                // Show preview
                previewImage.src = e.target.result;
                defaultPreview.classList.add('hidden');
                imagePreview.classList.remove('hidden');

                // Show image info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);

                // Get image dimensions
                const img = new Image();
                img.onload = function() {
                    fileDimensions.textContent = `${this.width} × ${this.height}px`;
                    imageInfo.classList.remove('hidden');
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Image actions
        window.replaceImage = function() {
            imageUpload.click();
        };

        window.removeImage = function() {
            // Reset to default
            previewImage.src = '';
            defaultPreview.classList.remove('hidden');
            imagePreview.classList.add('hidden');
            imageInfo.classList.add('hidden');
            imageUpload.value = '';
        };
    });
</script>

<?php include_once __DIR__ . '/../../../Layouts/Admin/Footer.php' ?>