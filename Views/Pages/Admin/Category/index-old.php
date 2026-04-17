<?php

include_once __DIR__ . '/../../../Layouts/Admin/AfterLogin.php';
$categories = $Categories['data'];
$meta = $Categories['meta'];

?>

<div class="max-w-[1520px] 2xl:w-full py-4 mx-5 2xl:mx-auto mb-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Category Management</h1>
            <p class="text-gray-600 mt-2">Manage your product categories</p>
        </div>
        <a href="<?= route('categories.create') ?>">
            <button class="bg-rose-100 hover:bg-rose-100/60 text-rose-600 text-sm px-4 py-2 rounded-lg font-semibold transition-colors cursor-pointer">
                Add Category
            </button>
        </a>
    </div>

    <div class="border border-dashed rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filters</h2>
        <form id="filterForm" method="GET" class="flex flex-wrap gap-4 items-center">
            <!-- Search Filter -->
            <div>
                <input name="search" id="searchFilter" class="w-40 h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] bg-[#f5f5f5]" placeholder="Search by name..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" id="statusFilter" class="w-32 h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] bg-[#f5f5f5]">
                    <option value="">Status</option>
                    <option value="Active" <?= isset($_GET['status']) && $_GET['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= isset($_GET['status']) && $_GET['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <!-- Limit Per Page -->
            <div>
                <select name="limit" id="limitFilter" class="w-16 h-12 outline-none font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] bg-[#f5f5f5]">
                    <option value="20" <?= isset($meta['limit']) && $meta['limit'] == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= isset($meta['limit']) && $meta['limit'] == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-200 hover:bg-gray-200/60 text-gray-800 px-5 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="?" class="border border-dashed hover:bg-gray-50 text-gray-800 px-5 py-2 rounded-lg font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

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

    <!-- Category Grid -->
    <div class="overflow-hidden">
        <?php if (empty($categories)): ?>
            <div class="border border-dashed rounded-xl text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <img src="<?= asset('assets/images/empty.png') ?>" alt="">
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No categories found</h3>
                <p class="text-gray-500 mb-8">Get started by creating a new category</p>

                <a href="<?= route('categories.create') ?>">
                    <button class="bg-rose-100 hover:bg-rose-100/60 text-rose-600 text-sm px-4 py-2 rounded-lg font-semibold transition-colors cursor-pointer">
                        Create Category
                    </button>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                <?php foreach ($categories as $category): ?>
                    <div class="border border-dashed rounded-xl overflow-hidden">
                        <!-- Category Image -->
                        <div class="relative h-48 bg-gray-100">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?= public_dir(htmlspecialchars($category['image'])) ?>"
                                    alt="<?= htmlspecialchars($category['name']) ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-folder text-gray-400 text-6xl"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $category['status'] == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= htmlspecialchars($category['status']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Category Info -->
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2 truncate">
                                <?= htmlspecialchars($category['name']) ?>
                            </h3>

                            <div class="text-sm text-gray-600 mb-3">
                                <p class="truncate flex items-start">
                                    <ion-icon name="attach-outline" class="text-base"></ion-icon>
                                    /<?= htmlspecialchars($category['slug']) ?>
                                </p>
                                <?php if (!empty($category['parent'])): ?>
                                    <p class="mt-2 truncate">
                                        <i class="fas fa-level-up-alt mr-1"></i>
                                        Parent: <?= htmlspecialchars($category['parent']['name']) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="mt-2 text-gray-400">
                                        <i class="fas fa-level-up-alt mr-1"></i>
                                        No parent category
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                <span class="text-xs text-gray-500">
                                    ID: <?= htmlspecialchars($category['id']) ?>
                                </span>
                                <div class="flex gap-2">
                                    <button class="edit-btn size-10 flex justify-center items-center bg-blue-50 hover:bg-blue-100 text-blue-600 p-2 rounded-lg transition-colors cursor-pointer"
                                        data-id="<?= htmlspecialchars($category['id']) ?>">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <button class="delete-btn size-10 flex justify-center items-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors cursor-pointer"
                                        data-id="<?= htmlspecialchars($category['id']) ?>"
                                        data-name="<?= htmlspecialchars($category['name']) ?>">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($meta['totalPage'] > 1): ?>
            <div class="border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-600 mb-4 md:mb-0">
                        Showing
                        <span class="font-semibold"><?= min(($meta['page'] - 1) * $meta['limit'] + 1, $meta['total']) ?></span>
                        to
                        <span class="font-semibold"><?= min($meta['page'] * $meta['limit'], $meta['total']) ?></span>
                        of
                        <span class="font-semibold"><?= $meta['total'] ?></span>
                        categories
                    </div>

                    <!-- Pagination Controls -->
                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $meta['page'] - 1)])) ?>"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors <?= $meta['page'] == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </a>

                        <!-- Page Numbers -->
                        <div class="flex space-x-1">
                            <?php
                            $currentPage = $meta['page'];
                            $totalPages = $meta['totalPage'];
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);

                            // Show first page if not in range
                            if ($startPage > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                                    1
                                </a>
                                <?php if ($startPage > 2): ?>
                                    <span class="w-10 h-10 flex items-center justify-center">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-lg <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?> transition-colors">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="w-10 h-10 flex items-center justify-center">...</span>
                                <?php endif; ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                                    <?= $totalPages ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Next Button -->
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages, $currentPage + 1)])) ?>"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors <?= $pagination['page'] == $totalPages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit button handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                alert(`Edit category with ID: ${categoryId}`);
                // In real implementation, you would:
                // 1. Open a modal with edit form
                // 2. Load category data via AJAX
                // 3. Submit form with PUT/PATCH request
            });
        });

        // Delete button handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                const categoryName = this.getAttribute('data-name');

                if (confirm(`Are you sure you want to delete "${categoryName}"?`)) {
                    // In real implementation, you would:
                    // 1. Send DELETE request to your API
                    // 2. Remove the card from DOM
                    // 3. Show success message
                    alert(`Category "${categoryName}" deleted!`);
                    this.closest('.bg-white.border').remove();
                }
            });
        });

        // Auto-submit filter form on input change (optional)
        document.getElementById('searchFilter').addEventListener('input', debounce(function(e) {
            const searchValue = e.target.value.trim();
            const url = new URL(window.location.href);

            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }

            // Reset to page 1 when searching
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }, 500));

        // Debounce utility function
        function debounce(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        // Auto-submit filter form on select change (optional)
        document.getElementById('limitFilter').addEventListener('change', function() {
            // Reset to page 1 when changing limit
            const url = new URL(window.location.href);
            url.searchParams.set('limit', this.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });

        // Optional: Auto-submit status filter on change
        document.getElementById('statusFilter').addEventListener('change', function() {
            const url = new URL(window.location.href);
            if (this.value) {
                url.searchParams.set('status', this.value);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.set('page', '1'); // Reset to page 1 when filtering
            window.location.href = url.toString();
        });
    });

    // Function to update URL without page reload (optional enhancement)
    function updateQueryParam(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        url.searchParams.set('page', '1'); // Reset to page 1 for new filters
        window.history.pushState({}, '', url.toString());
    }
</script>

<?php include_once __DIR__ . '/../../../Layouts/Admin/Footer.php' ?>