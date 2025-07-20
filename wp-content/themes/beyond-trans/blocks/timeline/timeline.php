<?php
$block_content = get_field('block_content');
$section_title = $block_content['section_title'];
$section_description = $block_content['section_description'];
$timeline_items = $block_content['timeline_items'];
$enable_auto_scroll = $block_content['enable_auto_scroll'] ?: true; 
$scroll_speed = $block_content['scroll_speed'] ?: 5000;


$timeline_id = 'timeline-' . uniqid();
?>

<section class="block timeline" id="<?= $timeline_id; ?>">
    <div class="timeline__background"></div>
    
    <div class="container">
        <div class="timeline__header">
            <?php if ($section_title) : ?>
                <h2 class="timeline__title fade-in"><?= esc_html($section_title); ?></h2>
            <?php endif; ?>

            <?php if ($section_description) : ?>
                <p class="timeline__description fade-in"><?= esc_html($section_description); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($timeline_items && is_array($timeline_items)) : ?>
            <div class="timeline__wrapper">
               
                <div class="timeline__controls">
                    <button class="timeline__nav timeline__nav--prev" aria-label="Previous item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"></polyline>
                        </svg>
                    </button>
                    <button class="timeline__nav timeline__nav--next" aria-label="Next item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9,18 15,12 9,6"></polyline>
                        </svg>
                    </button>
                </div>

                <div class="timeline__container" 
                     data-auto-scroll="<?= $enable_auto_scroll ? 'true' : 'false'; ?>"
                     data-scroll-speed="<?= esc_attr($scroll_speed); ?>">
                    <div class="timeline__track">
                        <?php foreach ($timeline_items as $index => $item) :
                            $year = $item['year'] ?: date('Y');
                            $month = $item['month'] ?: 'January';
                            $title = $item['title'] ?: 'Achievement Title';
                            $description = $item['description'] ?: '';
                            $image = $item['image'];
                            $link = $item['link'] ?: '';
                            $link_text = $item['link_text'] ?: 'Read More';
                            $category = $item['category'] ?: 'General';
                        ?>
                                        <div class="timeline__item <?= $link ? 'timeline__item--clickable' : ''; ?> timeline__item--<?= esc_attr(strtolower($category)); ?>"
                data-year="<?= esc_attr($year); ?>"
                <?= $link ? 'data-link="' . esc_url($link) . '"' : ''; ?>>
                                <div class="timeline__item__marker">
                                    <div class="timeline__item__marker__dot"></div>
                                    <div class="timeline__item__marker__line"></div>
                                </div>

                                <div class="timeline__item__content">
                                    <div class="timeline__item__header">
                                        <div class="timeline__item__date">
                                            <span class="timeline__item__year"><?= esc_html($year); ?></span>
                                            <span class="timeline__item__month"><?= esc_html($month); ?></span>
                                        </div>
                                        <div class="timeline__item__category category-<?= esc_attr(strtolower($category)); ?>"><?= esc_html($category); ?></div>
                                    </div>

                                    <div class="timeline__item__details">
                                    <h3 class="timeline__item__title"><?= esc_html($title); ?></h3>
                                     <?php if ($description): ?>
                                            <div class="timeline__item__description">
                                                <p><?= esc_html($description); ?></p>
                                            </div>
                                            <?php endif; ?>     

                                    <?php if ($image): ?>
                                        <div class="timeline__item__image">
                                            <img src="<?= esc_url($image['url']); ?>" 
                                                 alt="<?= esc_attr($image['alt'] ?: $title); ?>"
                                                 loading="lazy">
                                        </div>
                                    <?php endif; ?>

                                    </div> 
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeline = document.getElementById('<?= $timeline_id; ?>');
    
    if (timeline) {
        initializeTimeline(timeline);
        
        
        const clickableItems = timeline.querySelectorAll('.timeline__item--clickable');
        clickableItems.forEach(item => {
            item.addEventListener('click', function() {
                const link = this.getAttribute('data-link');
                if (link) {
                    window.open(link, '_blank');
                }
            });
        });
    }
});


function initializeTimeline(timelineElement) {
    const container = timelineElement.querySelector('.timeline__container');
    const track = timelineElement.querySelector('.timeline__track');
    const items = timelineElement.querySelectorAll('.timeline__item');
    const prevBtn = timelineElement.querySelector('.timeline__nav--prev');
    const nextBtn = timelineElement.querySelector('.timeline__nav--next');
    
    if (!container || !track || items.length === 0) {
        return;
    }
    
    let currentIndex = 0;
    const totalItems = items.length;
    const autoScroll = container.dataset.autoScroll === 'true';
    const scrollSpeed = parseInt(container.dataset.scrollSpeed) || 5000;
    const itemWidth = 344; // 320px + 24px gap
    let autoScrollInterval;
    
  
    function goToNext() {
        if (currentIndex < totalItems - 1) {
            currentIndex++;
            updatePosition();
        } else if (autoScroll) {
            currentIndex = 0;
            updatePosition();
        }
    }
    
    function goToPrev() {
        if (currentIndex > 0) {
            currentIndex--;
            updatePosition();
        }
    }
    
    function updatePosition() {
        if (window.innerWidth > 768) {
            const scrollPosition = -currentIndex * itemWidth;
            track.style.transform = `translateX(${scrollPosition}px)`;
        } else {
        
            const mobileItemWidth = window.innerWidth - 40; 
            const scrollPosition = -currentIndex * (mobileItemWidth + 16); 
            track.style.transform = `translateX(${scrollPosition}px)`;
        }
        updateNavigation();
    }
    
    function updateNavigation() {
        if (prevBtn) prevBtn.disabled = currentIndex === 0;
        if (nextBtn) nextBtn.disabled = currentIndex === totalItems - 1;
    }
    
    
    if (prevBtn) prevBtn.addEventListener('click', goToPrev);
    if (nextBtn) nextBtn.addEventListener('click', goToNext);
    
    
    function startAutoScroll(immediate = false) {
       
        stopAutoScroll();
        
        if (autoScroll) {
            if (immediate) {
               
                setTimeout(() => {
                    goToNext();
                    autoScrollInterval = setInterval(goToNext, scrollSpeed);
                }, 200); 
            } else {
                autoScrollInterval = setInterval(goToNext, scrollSpeed);
            }
        }
    }
    
    function stopAutoScroll() {
        if (autoScrollInterval) {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    }
    
    
    items.forEach(item => {
        item.addEventListener('mouseenter', stopAutoScroll);
        item.addEventListener('mouseleave', () => startAutoScroll(false));
    });
    
    
    updateNavigation();
    if (autoScroll) {
        startAutoScroll(true); 
    }
    
    window.addEventListener('resize', function() {
        updatePosition();
        if (autoScroll) {
            startAutoScroll(false);
        }
    });
}
</script>