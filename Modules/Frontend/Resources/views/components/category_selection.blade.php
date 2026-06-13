{{-- Category Selection Component --}}
<div class="category-selection-container">
    <h5 class="mb-4">{{ __('frontend.select_category') }}</h5>
    
    <div class="row g-3" id="categories-container">
        {{-- Categories will be loaded dynamically --}}
    </div>
    
    <div class="text-center mt-4" id="categories-loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading categories...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Category selection component loaded, serviceId:', {{ $serviceId ?? 'null' }});
    
    // Load categories immediately since we know the container exists
    loadServiceCategories();
});

function loadServiceCategories() {
    const serviceId = {{ $serviceId ?? 'null' }};
    
    console.log('Loading categories for service ID:', serviceId);
    
    if (!serviceId) {
        console.error('Service ID not provided');
        showError('Service ID not available');
        return;
    }
    
    // Direct API call since we know the container exists
    fetchCategories(serviceId);
}

function fetchCategories(serviceId) {
    const url = `/api/services/${serviceId}/categories`;
    console.log('Fetching categories from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Categories API response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Categories API response data:', data);
            if (data.success) {
                renderCategories(data.categories);
            } else {
                showError(data.message || 'Failed to load categories');
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            showError('Error loading categories: ' + error.message);
        })
        .finally(() => {
            const loadingElement = document.getElementById('categories-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        });
}

function renderCategories(categories) {
    const container = document.getElementById('categories-container');
    
    console.log('Rendering categories:', categories);
    console.log('Container found:', !!container);
    
    if (!container) {
        console.error('Categories container not found');
        return;
    }
    
    if (!categories || categories.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted text-center">No categories available for this service.</p></div>';
        return;
    }
    
    console.log('Rendering', categories.length, 'categories');
    
    const categoriesHTML = categories.map(category => {
        console.log('Rendering category:', category.name, 'Price:', category.price);
        return `
            <div class="col-lg-6 col-md-6">
                <div class="category-card card h-100 border-0 shadow-sm" 
                     data-category-id="${category.id}"
                     data-requires-doctor="${category.requires_doctor ? 'true' : 'false'}"
                     style="cursor: pointer; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="card-title mb-0 fw-semibold">${escapeHtml(category.name)}</h6>
                            <span class="badge bg-primary-subtle text-primary">£${category.price || '0'}</span>
                        </div>
                        
                        ${category.description ? `<p class="card-text text-muted small mb-3">${escapeHtml(category.description)}</p>` : ''}
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ph ph-${category.requires_doctor ? 'user-md' : 'test-tube'}"></i>
                                ${category.requires_doctor ? 'Doctor consultation' : 'No doctor required'}
                            </small>
                            <button class="btn btn-outline-primary btn-sm select-category-btn">
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    console.log('Setting innerHTML with', categoriesHTML.length, 'characters');
    container.innerHTML = categoriesHTML;
    
    // Add click handlers
    const categoryCards = document.querySelectorAll('.category-card');
    console.log('Adding click handlers to', categoryCards.length, 'category cards');
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            console.log('Category card clicked:', this.dataset.categoryId);
            selectCategory(this);
        });
    });
    
    console.log('Categories rendered successfully');
}

function selectCategory(cardElement) {
    // Remove previous selections
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary-subtle');
    });
    
    // Highlight selected card
    cardElement.classList.add('border-primary', 'bg-primary-subtle');
    
    const categoryId = cardElement.dataset.categoryId;
    const requiresDoctor = cardElement.dataset.requiresDoctor === 'true';
    
    // Store selection in session/form
    sessionStorage.setItem('selectedCategoryId', categoryId);
    sessionStorage.setItem('categoryRequiresDoctor', requiresDoctor);
    
    // Update next button state
    const nextButton = document.getElementById('nextButton');
    if (nextButton) {
        nextButton.disabled = false;
        nextButton.textContent = 'Continue';
        
        // Add click handler to trigger category selected event
        nextButton.onclick = function() {
            console.log('🚀 Next button clicked - triggering categorySelected event');
            
            // Trigger category selection event for enhanced-booking.js
            const event = new CustomEvent('categorySelected', {
                detail: { 
                    categoryId: categoryId, 
                    requiresDoctor: requiresDoctor 
                }
            });
            document.dispatchEvent(event);
        };
    }
    
    console.log('Category selected:', categoryId, 'Requires doctor:', requiresDoctor);
}

function showError(message) {
    const container = document.getElementById('categories-container');
    if (container) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="ph ph-warning-circle me-2"></i>
                    ${escapeHtml(message)}
                </div>
            </div>
        `;
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<style>
.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.category-card.border-primary {
    border-width: 2px !important;
}

.category-selection-container {
    padding: 20px 0;
}
</style>