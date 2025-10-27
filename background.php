<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="network-background-container">
    <canvas id="networkBackground"></canvas>
</div>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        
        #networkCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .content {
            position: relative;
            z-index: 1;
            color: #333333;
            padding: 50px;
            text-align: center;
        }
        
        h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(0, 100, 255, 0.3);
            color: #222222;
        }
        
        p {
            font-size: 1.2em;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            color: #555555;
        }
    </style>
</head>
<body>
    <canvas id="networkCanvas"></canvas>
    
    <!-- <div class="content">
        <h1>Network Background</h1>
        <p>Animasi jaringan dengan node dan koneksi yang bergerak secara dinamis. Perfect untuk background yang menarik!</p>
    </div> -->

    <script>
        class NetworkBackground {
            constructor() {
                this.canvas = document.getElementById('networkCanvas');
                this.ctx = this.canvas.getContext('2d');
                this.nodes = [];
                this.connections = [];
                this.mouse = { x: 0, y: 0, radius: 100 };
                
                this.init();
                this.animate();
                
                // Event listeners
                window.addEventListener('resize', () => this.init());
                window.addEventListener('mousemove', (e) => {
                    this.mouse.x = e.clientX;
                    this.mouse.y = e.clientY;
                });
            }
            
            init() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
                
                this.createNodes();
                this.createConnections();
            }
            
            createNodes() {
                this.nodes = [];
                const nodeCount = Math.min(80, Math.floor((window.innerWidth * window.innerHeight) / 10000));
                
                for (let i = 0; i < nodeCount; i++) {
                    this.nodes.push({
                        x: Math.random() * this.canvas.width,
                        y: Math.random() * this.canvas.height,
                        vx: (Math.random() - 0.5) * 0.5,
                        vy: (Math.random() - 0.5) * 0.5,
                        radius: Math.random() * 2 + 1,
                        baseRadius: Math.random() * 2 + 1,
                        color: this.getRandomColor()
                    });
                }
            }
            
            createConnections() {
                this.connections = [];
                // Connections will be created dynamically in animate method
            }
            
            getRandomColor() {
                const colors = [
                    '#0066cc', '#0099ff', '#00aaff', '#0088ff', '#0066ff',
                    '#0044cc', '#0055aa', '#0077cc', '#0099cc', '#00aacc'
                ];
                return colors[Math.floor(Math.random() * colors.length)];
            }
            
            animate() {
                requestAnimationFrame(() => this.animate());
                
                // Clear dengan background putih transparan untuk trail effect
                this.ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
                this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
                
                this.updateNodes();
                this.drawConnections();
                this.drawNodes();
            }
            
            updateNodes() {
                this.nodes.forEach(node => {
                    // Update position
                    node.x += node.vx;
                    node.y += node.vy;
                    
                    // Bounce off walls
                    if (node.x < 0 || node.x > this.canvas.width) node.vx *= -1;
                    if (node.y < 0 || node.y > this.canvas.height) node.vy *= -1;
                    
                    // Mouse interaction
                    const dx = node.x - this.mouse.x;
                    const dy = node.y - this.mouse.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < this.mouse.radius) {
                        const angle = Math.atan2(dy, dx);
                        const force = (this.mouse.radius - distance) / this.mouse.radius;
                        
                        node.vx += Math.cos(angle) * force * 0.5;
                        node.vy += Math.sin(angle) * force * 0.5;
                    }
                    
                    // Gentle radius pulse
                    node.radius = node.baseRadius + Math.sin(Date.now() * 0.002 + node.x) * 0.5;
                });
            }
            
            drawConnections() {
                this.connections = [];
                
                for (let i = 0; i < this.nodes.length; i++) {
                    for (let j = i + 1; j < this.nodes.length; j++) {
                        const node1 = this.nodes[i];
                        const node2 = this.nodes[j];
                        
                        const dx = node1.x - node2.x;
                        const dy = node1.y - node2.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < 150) {
                            const opacity = 1 - (distance / 150);
                            
                            this.ctx.beginPath();
                            this.ctx.strokeStyle = `rgba(0, 100, 200, ${opacity * 0.2})`;
                            this.ctx.lineWidth = 0.5;
                            this.ctx.moveTo(node1.x, node1.y);
                            this.ctx.lineTo(node2.x, node2.y);
                            this.ctx.stroke();
                            
                            this.connections.push({
                                node1: i,
                                node2: j,
                                opacity: opacity
                            });
                        }
                    }
                }
            }
            
            drawNodes() {
                this.nodes.forEach(node => {
                    // Glow effect yang lebih subtle untuk background putih
                    const gradient = this.ctx.createRadialGradient(
                        node.x, node.y, 0,
                        node.x, node.y, node.radius * 2
                    );
                    
                    gradient.addColorStop(0, node.color);
                    gradient.addColorStop(1, 'rgba(0, 100, 200, 0)');
                    
                    this.ctx.beginPath();
                    this.ctx.arc(node.x, node.y, node.radius * 2, 0, Math.PI * 2);
                    this.ctx.fillStyle = gradient;
                    this.ctx.fill();
                    
                    // Core node
                    this.ctx.beginPath();
                    this.ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                    this.ctx.fillStyle = node.color;
                    this.ctx.fill();
                });
            }
        }
        
        // Initialize the network background when page loads
        window.addEventListener('load', () => {
            new NetworkBackground();
        });
        
        // Add some interactive effects
        document.addEventListener('click', (e) => {
            const network = document.getElementById('networkCanvas');
            const ripple = document.createElement('div');
            
            ripple.style.position = 'fixed';
            ripple.style.left = (e.clientX - 50) + 'px';
            ripple.style.top = (e.clientY - 50) + 'px';
            ripple.style.width = '100px';
            ripple.style.height = '100px';
            ripple.style.border = '2px solid #0066cc';
            ripple.style.borderRadius = '50%';
            ripple.style.animation = 'ripple 1s linear';
            ripple.style.pointerEvents = 'none';
            ripple.style.zIndex = '1000';
            
            document.body.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 1000);
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                0% {
                    transform: scale(0.1);
                    opacity: 1;
                }
                100% {
                    transform: scale(3);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>