<?php
$block_content = get_field('block_content');
$section_title = $block_content['section_title'];
$section_description = $block_content['section_description'];
$timeline_items = $block_content['timeline_items'];
$enable_animations = $block_content['enable_animations'] ?? true;

$timeline_id = 'timeline-' . uniqid();
?>

<section class="block timeline timeline--vertical" id="<?= $timeline_id; ?>">
    <div class="timeline__background">
        <div class="timeline__background-gradient"></div>
        <div class="timeline__background-pattern"></div>
    </div>
    
    <div class="container">
        <div class="timeline__header">
            <?php if ($section_title) : ?>
                <h2 class="timeline__title" data-aos="fade-up"><?= esc_html($section_title); ?></h2>
            <?php endif; ?>

            <?php if ($section_description) : ?>
                <p class="timeline__description" data-aos="fade-up" data-aos-delay="100"><?= esc_html($section_description); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($timeline_items && is_array($timeline_items)) : ?>
            <div class="timeline__wrapper">
                <!-- Year Navigation -->
                <div class="timeline__years-nav" data-aos="fade-in" data-aos-delay="200">
                    <?php 
                    $years = array_unique(array_column($timeline_items, 'year'));
                    sort($years);
                    foreach ($years as $year) : ?>
                        <button class="timeline__year-btn" data-year="<?= esc_attr($year); ?>">
                            <?= esc_html($year); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Main Timeline -->
                <div class="timeline__container">
                    <div class="timeline__line">
                        <div class="timeline__line-progress"></div>
                    </div>
                    
                    <div class="timeline__items">
                        <?php foreach ($timeline_items as $index => $item) :
                            $year = $item['year'] ?: date('Y');
                            $month = $item['month'] ?: 'January';
                            $day = $item['day'] ?: '01';
                            $title = $item['title'] ?: 'Achievement Title';
                            $description = $item['description'] ?: '';
                            $image = $item['image'];
                            $link = $item['link'] ?: '';
                            $link_text = $item['link_text'] ?: 'Learn More';
                            $category = $item['category'] ?: 'General';
                            $icon = $item['icon'] ?: 'star';
                            $side = $index % 2 === 0 ? 'left' : 'right';
                        ?>
                            <div class="timeline__item timeline__item--<?= $side; ?> timeline__item--<?= esc_attr(strtolower($category)); ?>"
                                 data-year="<?= esc_attr($year); ?>"
                                 data-index="<?= $index; ?>"
                                 data-aos="fade-<?= $side === 'left' ? 'right' : 'left'; ?>"
                                 data-aos-delay="<?= 100 + ($index * 50); ?>">
                                
                                <!-- Timeline Node -->
                                <div class="timeline__node" data-year="<?= esc_attr($year); ?>">
                                    <div class="timeline__node-outer">
                                        <div class="timeline__node-inner">
                                            <span class="timeline__node-year"><?= esc_html($year); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Card -->
                                <div class="timeline__card">
                                    <div class="timeline__card-inner">
                                        <!-- Date Text -->
                                        <div class="timeline__date-text">
                                            <?= esc_html($month); ?> <?= esc_html($year); ?>
                                        </div>

                                        <!-- Category Tag -->
                                        <div class="timeline__category category-<?= esc_attr(strtolower($category)); ?>">
                                            <?= esc_html($category); ?>
                                        </div>

                                        <!-- Content -->
                                        <div class="timeline__content">
                                            <h3 class="timeline__item-title"><?= esc_html($title); ?></h3>
                                            
                                            <?php if ($description): ?>
                                                <div class="timeline__item-description">
                                                    <p><?= esc_html($description); ?></p>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($image): ?>
                                                <div class="timeline__item-image">
                                                    <img src="<?= esc_url($image['url']); ?>" 
                                                         alt="<?= esc_attr($image['alt'] ?: $title); ?>"
                                                         loading="lazy">
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($link): ?>
                                                <a href="<?= esc_url($link); ?>" class="timeline__item-link" target="_blank" rel="noopener">
                                                    <span><?= esc_html($link_text); ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M7 17L17 7M17 7H7M17 7V17"/>
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
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
    const itemWidth = 352; // 320px + 32px gap
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