// Initialize card effects on all elements with class 'mtg-card'
function initializeCardEffects() {
    document.querySelectorAll('.mtg-card').forEach(card => {
        new MTGCard3DTiltEffect(card);
    });
}

(function() {
    if (window.MTGCard3DTiltEffect) return;

    class MTGCard3DTiltEffect {
        constructor(cardElement) {
            this.card = cardElement;
            if (!this.card) throw new Error('No card element provided');

            this.shine = this.createShineElement();
            this.rainbowShine = this.createRainbowShineElement();
            this.rarity = this.card.dataset.rarity;

            // Enhanced settings with improved depth and shadows
            this.settings = {
                tiltEffectMaxRotation: this.getRarityBasedRotation(),
                tiltEffectPerspective: 2500, // Increased perspective for better depth
                tiltEffectScale: this.getRarityBasedScale(),
                shineMovementRange: 80, // Increased shine range
                rainbowShineMovementRange: 70,
                glowIntensity: this.getRarityBasedGlowIntensity(),
                transitionDuration: '0.5s',
                transitionEasing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                hoverLift: 8, // Increased lift for better separation
                depthOffset: 30, // Increased depth offset
                shadowIntensity: 0.15 // Base shadow intensity
            };

            // Add initial transform with enhanced perspective
            this.card.style.transform = `
                perspective(${this.settings.tiltEffectPerspective}px)
                rotateX(0)
                rotateY(0)
                scale3d(1, 1, 1)
                translateZ(0)
            `;

            this.setupEventListeners();
            this.injectStyles();
        }

        createShineElement() {
            return this.createAndAppendElement('shine-effect');
        }

        createRainbowShineElement() {
            const container = this.createAndAppendElement('rainbow-shine-container');
            const effect = this.createAndAppendElement('rainbow-shine-effect');
            container.appendChild(effect);
            return effect;
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
            const transition = active ? 'all 0.4s ease-out' : 'none';
            this.card.style.transition = transition;
            this.shine.style.transition = transition;
            this.rainbowShine.style.transition = transition;
        }

        handleTilt(e) {
            const { left, top, width, height } = this.card.getBoundingClientRect();
            const mouseX = e.clientX - left;
            const mouseY = e.clientY - top;
            
            // Enhanced angle calculations with smoothing
            const normalizedX = (mouseX - width / 2) / (width / 2);
            const normalizedY = (mouseY - height / 2) / (height / 2);
            
            const angleX = normalizedY * this.settings.tiltEffectMaxRotation;
            const angleY = normalizedX * this.settings.tiltEffectMaxRotation;
            
            // Enhanced distance calculation for dynamic effects
            const distanceFromCenter = Math.min(
                Math.sqrt(normalizedX * normalizedX + normalizedY * normalizedY),
                1
            );
            
            // Enhanced dynamic effects
            const scale = this.settings.tiltEffectScale - (distanceFromCenter * 0.015);
            const translateZ = this.settings.depthOffset * (1 - distanceFromCenter);
            const shadowIntensity = this.settings.shadowIntensity * (1 + distanceFromCenter);

            // Apply enhanced transform with improved depth and shadows
            this.card.style.transform = `
                perspective(${this.settings.tiltEffectPerspective}px)
                rotateX(${-angleX}deg)
                rotateY(${angleY}deg)
                scale3d(${scale}, ${scale}, ${scale})
                translateZ(${translateZ}px)
                translateY(${-this.settings.hoverLift}px)
            `;
            
            // Dynamic shadow based on tilt
            this.card.style.boxShadow = `
                0 ${14 + Math.abs(angleY) * 0.5}px ${30 + Math.abs(angleX)}px rgba(0,0,0,${shadowIntensity}),
                0 ${4 + Math.abs(angleY) * 0.1}px ${10 + Math.abs(angleX) * 0.2}px rgba(0,0,0,${shadowIntensity * 1.5})
            `;

            // Enhanced shine effects
            this.updateShineEffect(
                this.shine,
                angleY / this.settings.tiltEffectMaxRotation,
                angleX / this.settings.tiltEffectMaxRotation,
                this.settings.shineMovementRange
            );
            this.updateShineEffect(
                this.rainbowShine,
                angleY / this.settings.tiltEffectMaxRotation,
                angleX / this.settings.tiltEffectMaxRotation,
                this.settings.rainbowShineMovementRange
            );
        }

        updateShineEffect(element, angleX, angleY, range) {
            const x = angleX * range;
            const y = angleY * range;
            
            // Enhanced opacity calculation based on angle and distance
            const distanceFromCenter = Math.sqrt(angleX * angleX + angleY * angleY);
            const baseOpacity = Math.min(
                Math.max(distanceFromCenter * 2, 0.3),
                0.8
            );
            
            // Dynamic scale based on movement
            const scale = 1.2 + (distanceFromCenter * 0.1);
            
            // Apply enhanced shine effect
            element.style.transform = `translate(${x}%, ${y}%) scale(${scale})`;
            element.style.opacity = baseOpacity.toString();
            element.style.transition = `
                transform ${this.settings.transitionDuration} ${this.settings.transitionEasing},
                opacity ${this.settings.transitionDuration} ${this.settings.transitionEasing}
            `;
        }

        resetTilt() {
            this.setTransition(true);
            
            // Smooth reset animation
            this.card.style.transform = `
                perspective(${this.settings.tiltEffectPerspective}px)
                rotateX(0deg)
                rotateY(0deg)
                scale3d(1, 1, 1)
                translateZ(0)
            `;

            // Fade out shine effects
            [this.shine, this.rainbowShine].forEach(element => {
                element.style.transform = 'translate(0%, 0%) scale(1)';
                element.style.opacity = '0';
                element.style.transition = `all ${this.settings.transitionDuration} ${this.settings.transitionEasing}`;
            });
        }

        resetShineEffect(element) {
            element.style.transform = 'translate(0%, 0%)';
            element.style.opacity = '0';
        }

        getRarityBasedRotation() {
            switch(this.rarity) {
                case 'Mythic Rare': return 18;
                case 'Rare': return 15;
                case 'Uncommon': return 12;
                case 'Common': return 10;
                default: return 10;
            }
        }

        getRarityBasedScale() {
            switch(this.rarity) {
                case 'Mythic Rare': return 1.1;
                case 'Rare': return 1.08;
                case 'Uncommon': return 1.06;
                case 'Common': return 1.04;
                default: return 1.04;
            }
        }

        getRarityBasedGlowIntensity() {
            switch(this.rarity) {
                case 'Mythic Rare': return '0 0 30px rgba(255,140,0,0.4), 0 0 60px rgba(255,69,0,0.2)';
                case 'Rare': return '0 0 25px rgba(255,215,0,0.3), 0 0 50px rgba(218,165,32,0.15)';
                case 'Uncommon': return '0 0 20px rgba(192,192,192,0.25), 0 0 40px rgba(169,169,169,0.1)';
                case 'Common': return '0 0 15px rgba(128,128,128,0.2), 0 0 30px rgba(105,105,105,0.1)';
                default: return '0 0 15px rgba(128,128,128,0.2), 0 0 30px rgba(105,105,105,0.1)';
            }
        }

        injectStyles() {
            if (!document.getElementById('mtg-card-3d-tilt-effect-styles')) {
                const style = document.createElement('style');
                style.id = 'mtg-card-3d-tilt-effect-styles';
                style.textContent = `
                    .mtg-card {
                        transition: transform 0.2s ease-out;
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
                            rgba(255, 255, 255, 0.8) 0%,
                            rgba(255, 255, 255, 0.6) 20%,
                            rgba(255, 255, 255, 0.4) 40%,
                            rgba(255, 255, 255, 0.2) 60%,
                            rgba(255, 255, 255, 0.1) 80%,
                            rgba(255, 255, 255, 0) 100%
                        );
                        pointer-events: none;
                        opacity: 0;
                        transition: opacity 0.4s ease-out, transform 0.4s ease-out;
                        mix-blend-mode: overlay;
                        filter: blur(3px);
                    }

                    .rainbow-shine-container {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        overflow: hidden;
                        pointer-events: none;
                    }

                    .mythic-holographic {
                        background: linear-gradient(125deg, 
                            rgba(255,0,0,0.3),
                            rgba(255,165,0,0.3),
                            rgba(255,255,0,0.3),
                            rgba(0,255,0,0.3),
                            rgba(0,0,255,0.3),
                            rgba(75,0,130,0.3),
                            rgba(238,130,238,0.3)
                        );
                        animation: holographic 4s ease-in-out infinite;
                        box-shadow: 
                            inset 0 0 50px rgba(255,140,0,0.3),
                            0 0 20px rgba(255,69,0,0.2);
                    }

                    .rare-holographic {
                        background: linear-gradient(125deg,
                            rgba(255,215,0,0.25),
                            rgba(255,255,255,0.3),
                            rgba(255,215,0,0.25)
                        );
                        animation: holographic 3s ease-in-out infinite;
                        box-shadow: 
                            inset 0 0 40px rgba(255,215,0,0.2),
                            0 0 15px rgba(218,165,32,0.15);
                    }

                    .uncommon-holographic {
                        background: linear-gradient(125deg,
                            rgba(192,192,192,0.2),
                            rgba(255,255,255,0.25),
                            rgba(192,192,192,0.2)
                        );
                        animation: holographic 2.5s ease-in-out infinite;
                        box-shadow: 
                            inset 0 0 30px rgba(192,192,192,0.15),
                            0 0 10px rgba(169,169,169,0.1);
                    }

                    .rainbow-shine-effect {
                        position: absolute;
                        top: -50%;
                        left: -50%;
                        right: -50%;
                        bottom: -50%;
                        background: conic-gradient(
                            from 0deg,
                            rgba(255,0,0,0.2) 0deg,
                            rgba(255,165,0,0.2) 60deg,
                            rgba(255,255,0,0.2) 120deg,
                            rgba(0,255,0,0.2) 180deg,
                            rgba(0,0,255,0.2) 240deg,
                            rgba(75,0,130,0.2) 300deg,
                            rgba(238,130,238,0.2) 360deg
                        );
                        opacity: 0;
                        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
                        mix-blend-mode: color-dodge;
                        filter: blur(5px);
                    }

                    @keyframes holographic {
                        0% { 
                            filter: hue-rotate(0deg) brightness(1) saturate(1);
                            transform: translateZ(0);
                        }
                        25% {
                            filter: hue-rotate(90deg) brightness(1.1) saturate(1.1);
                            transform: translateZ(20px);
                        }
                        50% { 
                            filter: hue-rotate(180deg) brightness(1.2) saturate(1.2);
                            transform: translateZ(40px);
                        }
                        75% {
                            filter: hue-rotate(270deg) brightness(1.1) saturate(1.1);
                            transform: translateZ(20px);
                        }
                        100% { 
                            filter: hue-rotate(360deg) brightness(1) saturate(1);
                            transform: translateZ(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }
    }

    window.MTGCard3DTiltEffect = MTGCard3DTiltEffect;
})();
