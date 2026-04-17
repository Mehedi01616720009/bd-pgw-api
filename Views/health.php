<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD PGW API - System Health & Overview</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d9488;
            --primary-glow: rgba(13, 148, 136, 0.3);
            --bg-color: #f8fafc;
            --surface-color: rgba(255, 255, 255, 0.85);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent-bkash: #e2136e;
            --accent-nagad: #f15922;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image:
                radial-gradient(at 0% 0%, rgba(13, 148, 136, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(226, 19, 110, 0.05) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            background: var(--surface-color);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 24px;
            padding: 3rem;
            animation: fadeIn 1s ease-out forwards;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #15803d;
            padding: 8px 16px;
            border-radius: 99px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
            animation: pulse 2s infinite;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.3);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 1.125rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            background: #ffffff;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card.bkash::before {
            background: var(--accent-bkash);
        }

        .card.nagad::before {
            background: var(--accent-nagad);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
            color: #334155;
            font-size: 0.95rem;
        }

        .feature-list li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: bold;
        }

        .api-tester {
            margin-top: 3rem;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .api-tester h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
            color: #0f172a;
        }

        .endpoint-row {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
            justify-content: space-between;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .endpoint-row .method {
            background: #3b82f6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 12px;
        }

        .endpoint-row .method.post {
            background: #10b981;
        }

        .run-btn {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            font-weight: 600;
        }

        .run-btn:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .response-area {
            background: #ffffff;
            padding: 1rem;
            border-radius: 8px;
            color: #6d28d9;
            border: 1px solid #e2e8f0;
            font-family: monospace;
            min-height: 60px;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 2rem;
            }

            h1 {
                font-size: 2.25rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="status-badge">
                <div class="status-dot"></div>
                API is Operational
            </div>
            <h1>BD Payment Gateway API</h1>
            <p class="subtitle">A unified, secure, and lightning-fast PHP microservice dedicated to handling Bangladeshi Mobile Financial Services with enterprise-grade encryption.</p>
        </header>

        <div class="grid">
            <div class="card bkash">
                <h3><span style="color: var(--accent-bkash);">bKash</span> Integration</h3>
                <p>Fully functional Tokenized Checkout providing secure payment initialization and execution flows.</p>
                <ul class="feature-list">
                    <li>Dynamic OAuth token generation</li>
                    <li>Automated token refreshing mechanisms</li>
                    <li>Two-step checkout & execution</li>
                </ul>
            </div>

            <div class="card nagad">
                <h3><span style="color: var(--accent-nagad);">Nagad</span> Integration</h3>
                <p>Robust implementation of Nagad's secure cryptographic payment initialization protocol.</p>
                <ul class="feature-list">
                    <li>Asymmetric RSA key encryption</li>
                    <li>Digital Signature generation</li>
                    <li>Sensitive data obfuscation</li>
                </ul>
            </div>
        </div>

        <div class="api-tester">
            <h3>Health Check API</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem;">Test the live health check endpoint directly from your browser.</p>

            <div class="endpoint-row">
                <div>
                    <span class="method post">POST</span>
                    <span style="color: #2563eb;">/api/site/health</span>
                </div>
                <button class="run-btn" id="pingBtn">Ping Server</button>
            </div>

            <div class="response-area" id="responseArea" style="color: #64748b;">
                Waiting for request...
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pingBtn').addEventListener('click', async () => {
            const responseArea = document.getElementById('responseArea');
            responseArea.style.color = '#64748b';
            responseArea.textContent = 'Pinging...';

            const btn = document.getElementById('pingBtn');
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => {
                btn.style.transform = 'none';
            }, 150);

            try {
                const startTime = performance.now();
                const res = await fetch('/api/site/health', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                const endTime = performance.now();
                const latency = Math.round(endTime - startTime);

                responseArea.style.color = '#059669';
                responseArea.innerHTML = `<div><span style="color: #0d9488;">[${latency}ms]</span> ${JSON.stringify(data)}</div>`;
            } catch (err) {
                responseArea.style.color = '#dc2626';
                responseArea.textContent = `Error: Cannot connect to server.`;
            }
        });
    </script>
</body>

</html>