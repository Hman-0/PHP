// Hàm để tạo XMLHttpRequest
function createXHR() {
    if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else {
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
}

// Hàm debounce để giới hạn số lần gọi hàm
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

// 1. AJAX Intro - Lấy chi tiết sản phẩm theo ID
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện click vào nút xem chi tiết sản phẩm
    document.querySelectorAll('.view-detail').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            getProductDetail(productId);
        });
    });

    // Xử lý sự kiện click vào sản phẩm trong kết quả tìm kiếm
    document.addEventListener('click', function(e) {
        if (e.target.closest('.search-item')) {
            const productId = e.target.closest('.search-item').getAttribute('data-id');
            getProductDetail(productId);
            document.getElementById('search-results').style.display = 'none';
        }
    });

    // Xử lý sự kiện chọn danh mục để lấy thương hiệu
    const categorySelect = document.getElementById('category-select');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const category = this.value;
            if (category) {
                getBrands(category);
            } else {
                document.getElementById('brand-container').innerHTML = '';
            }
        });
    }

    // Xử lý sự kiện nhập từ khóa tìm kiếm
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const keyword = this.value.trim();
            if (keyword.length >= 1) { 
                searchProducts(keyword);
            } else {
                document.getElementById('search-results').style.display = 'none';
            }
        }, 300));
    }

    // Xử lý sự kiện gửi form bình chọn
    const pollForm = document.getElementById('poll-form');
    if (pollForm) {
        pollForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedOption = document.querySelector('input[name="poll"]:checked');
            if (selectedOption) {
                submitPoll(selectedOption.value);
            } else {
                alert('Vui lòng chọn một lựa chọn.');
            }
        });
    }
});

// Hàm lấy chi tiết sản phẩm bằng XMLHttpRequest
function getProductDetail(productId) {
    const xhr = createXHR();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('product-detail-container').innerHTML = xhr.responseText;
            document.getElementById('product-detail-container').style.display = 'block';
            
            // Thêm sự kiện cho các tab
            setupTabs();
            
            // Thêm sự kiện cho nút thêm vào giỏ hàng
            setupAddToCart();
            
            // Thêm sự kiện cho tab đánh giá
            const reviewsTab = document.getElementById('reviews-tab');
            if (reviewsTab) {
                reviewsTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadReviews(productId);
                });
            }
            
            // Cuộn đến phần chi tiết sản phẩm
            document.getElementById('product-detail-container').scrollIntoView({ behavior: 'smooth' });
        }
    };
    xhr.open('GET', 'product_detail.php?id=' + productId, true);
    xhr.send();
}

// Hàm thiết lập các tab
function setupTabs() {
    document.querySelectorAll('.tabs li a').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href').substring(1);
            
            // Ẩn tất cả các tab-pane
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Hiển thị tab-pane được chọn
            document.getElementById(target).classList.add('active');
            
            // Cập nhật trạng thái active cho tab
            document.querySelectorAll('.tabs li').forEach(li => {
                li.classList.remove('active');
            });
            this.parentElement.classList.add('active');
        });
    });
}

// Hàm thiết lập sự kiện thêm vào giỏ hàng
function setupAddToCart() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId);
        });
    });
}

// 2. AJAX PHP - Thêm sản phẩm vào giỏ hàng
function addToCart(productId) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng sản phẩm trong giỏ hàng
            document.getElementById('cart-count').textContent = data.cartCount;
            
            // Hiển thị thông báo
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.');
    });
}

// 3. AJAX Database - Hiển thị đánh giá sản phẩm
function loadReviews(productId) {
    fetch('reviews.php?product_id=' + productId)
    .then(response => response.text())
    .then(data => {
        document.getElementById('reviews-content').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('reviews-content').innerHTML = '<p>Đã xảy ra lỗi khi tải đánh giá.</p>';
    });
}

// 4. AJAX XML - Lấy danh sách thương hiệu từ file XML
function getBrands(category) {
    fetch('brands.php?category=' + encodeURIComponent(category))
    .then(response => response.text())
    .then(data => {
        document.getElementById('brand-container').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('brand-container').innerHTML = '<p>Đã xảy ra lỗi khi tải danh sách thương hiệu.</p>';
    });
}

// 5. AJAX Live Search - Tìm kiếm sản phẩm theo thời gian thực
function searchProducts(keyword) {
    fetch('search.php?keyword=' + encodeURIComponent(keyword))
    .then(response => response.text())
    .then(data => {
        const searchResults = document.getElementById('search-results');
        searchResults.innerHTML = data;
        searchResults.style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// 6. AJAX Poll - Bình chọn tính năng cần cải thiện
function submitPoll(option) {
    fetch('poll.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'option=' + encodeURIComponent(option)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiển thị kết quả bình chọn
            const pollResults = document.getElementById('poll-results');
            let resultsHTML = '<h4>Kết quả bình chọn (Tổng: ' + data.totalVotes + ' lượt)</h4>';
            
            data.results.forEach(result => {
                resultsHTML += '<div class="poll-label">';
                resultsHTML += '<span>' + result.option + '</span>';
                resultsHTML += '<span>' + result.percentage + '%</span>';
                resultsHTML += '</div>';
                resultsHTML += '<div class="poll-bar" style="width: ' + result.percentage + '%"></div>';
            });
            
            pollResults.innerHTML = resultsHTML;
            
            // Ẩn form bình chọn
            document.getElementById('poll-form').style.display = 'none';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi gửi bình chọn.');
    });
}