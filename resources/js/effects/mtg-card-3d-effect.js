export class MTGCard3DTiltEffect {
    constructor(cardElement) {
        this.card = cardElement;
        if (!this.card) throw new Error('No card element provided');

        this.shine = this.createShineElement();
        this.rarity = this.card.dataset.rarity;

        // Simplified settings with reduced effects
        this.settings = {
            tiltEffectMaxRotation: 8, // Reduced from variable rotation
            tiltEffectPerspective: 2000,
            tiltEffectScale: 1, // Removed scaling effect
            shineMovementRange: 60,
            transitionDuration: '0.3s',
            transitionEasing: 'ease-out',
            hoverLift: 2, // Reduced from 8px
            shadowIntensity: 0.1
        };

        this.card.style.transform = `
            perspective(${this.settings.tiltEffectPerspective}px)
            rotateX(0)
            rotateY(0)
            translateZ(0)
        `;

        this.setupEventListeners();
        this.injectStyles();
    }

    createShineElement() {
        return this.createAndAppendElement('shine-effect');
    }

    createAndAppendElement(className) {
        const element = document.createElement('div');
        element.classList.add(className);
        this.card.appendChild(element);
        return element;
    }

    setupEventListeners() {
        this.card.addEventListener('mouseenter', () => this.setTransition(false));
        this.card.addEventListener('mousemove', (e) => this.handleTilt(e));
        this.card.addEventListener('mouseleave', () => this.resetTilt());
    }

    setTransition(active) {
        const transition = active ? `all ${this.settings.transitionDuration} ${this.settings.transitionEasing}` : 'none';
        this.card.style.transition = transition;
        this.shine.style.transition = transition;
    }

    handleTilt(e) {
        const { left, top, width, height } = this.card.getBoundingClientRect();
        const mouseX = e.clientX - left;
        const mouseY = e.clientY - top;
        
        const normalizedX = (mouseX - width / 2) / (width / 2);
        const normalizedY = (mouseY - height / 2) / (height / 2);
        
        const angleX = normalizedY * this.settings.tiltEffectMaxRotation;
        const angleY = normalizedX * this.settings.tiltEffectMaxRotation;
        
        const shadowIntensity = this.settings.shadowIntensity;

        this.card.style.transform = `
            perspective(${this.settings.tiltEffectPerspective}px)
            rotateX(${-angleX}deg)
            rotateY(${angleY}deg)
            translateY(${-this.settings.hoverLift}px)
        `;
        
        this.card.style.boxShadow = `
            0 ${4 + Math.abs(angleY) * 0.2}px ${8 + Math.abs(angleX) * 0.2}px rgba(0,0,0,${shadowIntensity})
        `;

        this.updateShineEffect(
            this.shine,
            angleY / this.settings.tiltEffectMaxRotation,
            angleX / this.settings.tiltEffectMaxRotation,
            this.settings.shineMovementRange
        );
    }

    updateShineEffect(element, angleX, angleY, range) {
        const x = angleX * range;
        const y = angleY * range;
        
        const distanceFromCenter = Math.sqrt(angleX * angleX + angleY * angleY);
        const baseOpacity = Math.min(
            Math.max(distanceFromCenter * 2, 0.2),
            0.6
        );
        
        element.style.transform = `translate(${x}%, ${y}%)`;
        element.style.opacity = baseOpacity.toString();
    }

    resetTilt() {
        this.setTransition(true);
        
        this.card.style.transform = `
            perspective(${this.settings.tiltEffectPerspective}px)
            rotateX(0deg)
            rotateY(0deg)
            translateZ(0)
        `;

        this.shine.style.transform = 'translate(0%, 0%)';
        this.shine.style.opacity = '0';
    }

    injectStyles() {
        if (!document.getElementById('mtg-card-3d-tilt-effect-styles')) {
            const style = document.createElement('style');
            style.id = 'mtg-card-3d-tilt-effect-styles';
            style.textContent = `
                .mtg-card {
                    transition: transform ${this.settings.transitionDuration} ${this.settings.transitionEasing};
                    transform-style: preserve-3d;
                    will-change: transform;
                    position: relative;
                    overflow: hidden;
                }

                .shine-effect {
                    position: absolute;
                    top: -100%;
                    left: -100%;
                    right: -100%;
                    bottom: -100%;
                    background: radial-gradient(
                        circle at 50% 50%,
                        rgba(255, 255, 255, 0.7) 0%,
                        rgba(255, 255, 255, 0.5) 20%,
                        rgba(255, 255, 255, 0.3) 40%,
                        rgba(255, 255, 255, 0.1) 60%,
                        rgba(255, 255, 255, 0) 100%
                    );
                    pointer-events: none;
                    opacity: 0;
                    transition: opacity ${this.settings.transitionDuration} ${this.settings.transitionEasing},
                                transform ${this.settings.transitionDuration} ${this.settings.transitionEasing};
                    mix-blend-mode: overlay;
                    filter: blur(3px);
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// Create a helper function to initialize the effect
export function initializeMTGCard3DEffect(cardElement) {
    return new MTGCard3DTiltEffect(cardElement);
}
