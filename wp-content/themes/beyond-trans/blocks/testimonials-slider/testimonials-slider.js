jQuery(document).ready(function($) {
    var $slider = $('.testimonials-slider__slick');
    if ($slider.length) {
        var slideCount = $slider.children().length;
        var slickOptions;

        // Add a data attribute for CSS targeting
        $slider.attr('data-slide-count', slideCount);

        if (slideCount < 3) {
            slickOptions = {
                dots: true,
                infinite: false, // No looping for < 3 slides
                speed: 300,
                slidesToShow: slideCount, // Show actual number of slides
                slidesToScroll: slideCount, // Scroll actual number of slides
                adaptiveHeight: true,
                autoplay: false,
                autoplaySpeed: 5000,
                arrows: false, // Typically no arrows if not enough to slide
                centerMode: false, // Ensure not centered
                responsive: [
                    {
                        breakpoint: 768, // Mobile breakpoint (<= 767px)
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                    // No tablet breakpoint needed if slideCount is 1 or 2, as desktop already shows slideCount
                ]
            };
            // If slideCount is 2, tablet should also show 2, mobile 1.
            // If slideCount is 1, all sizes show 1.
            // The default slidesToShow handles this, only mobile needs explicit override for slideCount=2.
        } else { // slideCount >= 3
            slickOptions = {
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 3,
                adaptiveHeight: true,
                arrows: false,
                autoplay: true,
                autoplaySpeed: 5000,
                responsive: [
                    {
                        breakpoint: 992, // Tablet breakpoint (<= 991px)
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 768, // Mobile breakpoint (<= 767px)
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            };
        }
        $slider.slick(slickOptions);
    }
}); 