 <div class="modal fade rating-modal" id="review-service" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg rounded">
            <div class="modal-body modal-body-inner rate-us-modal">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>                                                                
                </div>
                <div class="text-center">
                    <form id="reviewForm">
                        <div class="mt-5 pt-3 text-center rate-box">
                            <h5>Rate our service now!</h5>
                            <p class="mb-5">Your feedback will improve our service.</p>

                            <div class="mt-5 pt-3">
                                <ul class="list-inline m-0 p-0 d-flex align-items-center justify-content-center gap-3 rating-list">
                                    <li data-value="1" class="star">
                                        <span class="text-warning fs-4 icon">
                                            <i class="ph-fill ph-star icon-fill"></i>
                                            <i class="ph ph-star icon-normal"></i>
                                        </span>
                                    </li>
                                    <li data-value="2" class="star">
                                        <span class="text-warning fs-4 icon">
                                            <i class="ph-fill ph-star icon-fill"></i>
                                            <i class="ph ph-star icon-normal"></i>
                                        </span>
                                    </li>
                                    <li data-value="3" class="star">
                                        <span class="text-warning fs-4 icon">
                                            <i class="ph-fill ph-star icon-fill"></i>
                                            <i class="ph ph-star icon-normal"></i>
                                        </span>
                                    </li>
                                    <li data-value="4" class="star">
                                        <span class="text-warning fs-4 icon">
                                            <i class="ph-fill ph-star icon-fill"></i>
                                            <i class="ph ph-star icon-normal"></i>
                                        </span>
                                    </li>
                                    <li data-value="5" class="star">
                                        <span class="text-warning fs-4 icon">
                                            <i class="ph-fill ph-star icon-fill"></i>
                                            <i class="ph ph-star icon-normal"></i>
                                        </span>
                                    </li>
                                </ul>

                                <div id="rating-error" class="text-danger d-none">
                                    {{ __('Please select a rating') }}
                                </div>

                                <div class="mt-5">
                                    <textarea class="form-control" placeholder="Share your experience to help others make informed healthcare decisions." rows="4" id="reviewTextarea"></textarea>
                                </div>

                                <div class="mt-5 pt-2">
                                    <button type="submit" class="btn btn-secondary" id="submitBtn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
<script>
    $(document).ready(function () {
        let selectedRating = 0;   
        let serviceId = null;     
        let doctorId = null;      
        let reviewId = null;      
        
        const stars = document.querySelectorAll('.star');
        const ratingError = document.getElementById('rating-error');
        const reviewForm = document.getElementById('reviewForm');
        const reviewTextarea = document.getElementById('reviewTextarea');

        function showRatingError(show = true) {
            if (show) {
                ratingError.classList.remove('d-none');
                ratingError.classList.add('d-block', 'mt-2');
            } else {
                ratingError.classList.remove('d-block');
                ratingError.classList.add('d-none');
            }
        }

        function highlightStars(rating) {
            $('.star').each(function () {
                const starValue = $(this).data('value');
                $(this).toggleClass('selected', starValue <= rating);
            });
        }

        function resetRating() {
            selectedRating = 0;
            $('.star').removeClass('selected');
            showRatingError(false);
            reviewTextarea.value = '';
        }

        $('.star').on('click', function () {
            selectedRating = $(this).data('value');
            highlightStars(selectedRating);
            showRatingError(false); 
        });

        $('#reviewForm').on('submit', function (event) {
            event.preventDefault();

            if (!selectedRating) {
                showRatingError(true);
                return false;
            }

            const reviewText = reviewTextarea.value.trim();

            const url = '{{ route('save-rating') }}?is_ajax=1';
            
            $.ajax({
                url: url,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    service_id: serviceId,
                    doctor_id: doctorId,
                    rating: selectedRating,
                    review_msg: reviewText,
                    id: reviewId
                }),
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        text: data.message
                    });
                    $('#review-service').modal('hide');
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        text: 'An error occurred while submitting your review.'
                    });
                }
            });
        });

        $('#review-service').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            serviceId = button.data('service-id');
            doctorId = button.data('doctor-id');
            reviewId = button.data('review-id');
            const rating = button.data('rating');
            const reviewMsg = button.data('review-msg');

            showRatingError(false); 

            if (reviewId) {
                selectedRating = rating;
                reviewTextarea.value = reviewMsg;
                highlightStars(selectedRating);
            } else {
                resetRating();
            }
        });

        $('#review-service').on('hidden.bs.modal', function () {
            resetRating(); 
        });

        $('.star').hover(
            function() {
                const hoverValue = $(this).data('value');
                highlightStars(hoverValue);
            },
            function() {
                highlightStars(selectedRating); 
            }
        );
    });
</script>
@endpush

<style>
.star {
    cursor: pointer; 
    transition: transform 0.3s ease;  
}

.star:hover {
    transform: scale(1.2);  
}

.star.selected .icon-fill {
    display: inline-block;  
}

.star.selected .icon-normal {
    display: none;  
}

.icon-fill {
    display: none; 
}

.icon-normal {
    display: inline-block; 
}
</style>
