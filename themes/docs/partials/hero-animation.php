<canvas id="hero-grid"></canvas>

<style>
    #hero-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        opacity: 0.6; /* Overall opacity */
        /* Fade out edges */
        -webkit-mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
        mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
    }
</style>

<script>
(function() {
    const canvas = document.getElementById('hero-grid');
    const ctx = canvas.getContext('2d');
    
    // Configuration
    const GRID_SPACING = 32; // 2rem
    const GRID_OFFSET = 16;   // Align with center of 2rem (32px) squares
    const DOT_SIZE = 1;      // Larger for visibility
    const HOVER_RADIUS = 300;
    
    let width, height;
    let mouseX = -1000;
    let mouseY = -1000;
    
    // Colors
    let baseColor = 'rgba(150, 150, 150, 0.3)'; // Increased opacity
    let accentColor = 'rgba(99, 102, 241, 1)'; 
    
    function updateColors() {
        const style = getComputedStyle(document.body);
        
        // Use text-secondary which is darker/higher contrast than tertiary
        const textSecondary = style.getPropertyValue('--text-secondary').trim();
        baseColor = textSecondary || 'rgba(150, 150, 150, 0.4)';
        
        // Parse accent for active dots
        const accent = style.getPropertyValue('--accent').trim();
        accentColor = accent || '#6366f1';
    }

    function resize() {
        // Use offsetWidth/Height to get rendered size
        const parent = canvas.parentElement;
        width = canvas.width = parent.offsetWidth;
        height = canvas.height = parent.offsetHeight;
        updateColors();
    }
    
    function draw() {
        ctx.clearRect(0, 0, width, height);
        
        // Calculate visible range of dots to avoid drawing offscreen
        // (Though for a hero section, usually small enough)
        const cols = Math.ceil(width / GRID_SPACING);
        const rows = Math.ceil(height / GRID_SPACING);
        
        // We want dots at multiples of spacing. 
        // Example: 32, 64, 96... aligns with 2rem (32px) padding/corners
        
        for (let i = 1; i < cols; i++) {
            for (let j = 1; j < rows; j++) {
                const x = i * GRID_SPACING + GRID_OFFSET;
                const y = j * GRID_SPACING + GRID_OFFSET;
                
                // Calculate distance to mouse
                const dx = x - mouseX;
                const dy = y - mouseY;
                const distSq = dx * dx + dy * dy;
                const hoverRadSq = HOVER_RADIUS * HOVER_RADIUS;
                
                // Always draw base dot for consistency
                ctx.fillStyle = baseColor;
                ctx.globalAlpha = 0.35;
                ctx.beginPath();
                ctx.arc(x, y, DOT_SIZE, 0, Math.PI * 2);
                ctx.fill();

                if (distSq < hoverRadSq) {
                    // Calculate intensity based on distance (0 to 1)
                    const intensity = 1 - (distSq / hoverRadSq);
                    
                    // Draw accent dot overlay
                    ctx.beginPath();
                    ctx.fillStyle = accentColor;
                    ctx.globalAlpha = intensity; // Smooth fade in
                    ctx.arc(x, y, DOT_SIZE, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
        }
        
        requestAnimationFrame(draw);
    }
    
    // Event Listeners
    window.addEventListener('resize', resize);
    
    document.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;
    });
    
    // Init
    resize();
    draw();
    
})();
</script>