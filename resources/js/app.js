const chartDataElement = document.querySelector("#dashboard-chart-data");
const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
).matches;
const rupiah = new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
});

if (chartDataElement && document.querySelector("[data-chart]")) {
    const chartData = JSON.parse(chartDataElement.textContent);

    void import("chart.js")
        .then(
            ({
                BarController,
                BarElement,
                CategoryScale,
                Chart,
                ArcElement,
                DoughnutController,
                Filler,
                Legend,
                LineController,
                LineElement,
                LinearScale,
                PointElement,
                Tooltip,
            }) => {
                Chart.register(
                    BarController,
                    BarElement,
                    CategoryScale,
                    Filler,
                    Legend,
                    ArcElement,
                    DoughnutController,
                    LineController,
                    LineElement,
                    LinearScale,
                    PointElement,
                    Tooltip,
                );
                Chart.defaults.color = "#64748b";
                Chart.defaults.font.family = getComputedStyle(
                    document.body,
                ).fontFamily;
                Chart.defaults.font.size = 11;
                const markChartLoaded = (canvas) => {
                    const surface = canvas.closest(".chart-surface");
                    surface.classList.add("is-loaded");
                    surface.setAttribute("aria-busy", "false");
                };

                const sharedOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: prefersReducedMotion ? false : { duration: 450 },
                    interaction: { intersect: false, mode: "index" },
                    plugins: {
                        tooltip: {
                            padding: 10,
                            displayColors: true,
                            callbacks: {
                                label: (context) =>
                                    `${context.dataset.label}: ${rupiah.format(context.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { maxRotation: 0, minRotation: 0 },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: "#e2e8f0" },
                            border: { display: false },
                            ticks: {
                                callback: (value) =>
                                    `Rp ${new Intl.NumberFormat("id-ID", { notation: "compact", maximumFractionDigits: 1 }).format(value)}`,
                            },
                        },
                    },
                };

                const balanceCanvas = document.querySelector(
                    '[data-chart="balance"]',
                );
                if (balanceCanvas) {
                    new Chart(balanceCanvas, {
                        type: "line",
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: "Saldo akhir bulan",
                                    data: chartData.balance,
                                    borderColor: "#0f172a",
                                    backgroundColor: "rgba(212, 167, 44, 0.13)",
                                    fill: true,
                                    tension: 0.35,
                                    borderWidth: 2.5,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: "#d4a72c",
                                },
                            ],
                        },
                        options: {
                            ...sharedOptions,
                            plugins: {
                                ...sharedOptions.plugins,
                                legend: { display: false },
                            },
                        },
                    });
                }

                const cashflowCanvas = document.querySelector(
                    '[data-chart="cashflow"]',
                );
                if (cashflowCanvas) {
                    new Chart(cashflowCanvas, {
                        type: "bar",
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: "Setoran",
                                    data: chartData.deposits,
                                    backgroundColor: "#d4a72c",
                                    borderRadius: 4,
                                    maxBarThickness: 24,
                                },
                                {
                                    label: "Penarikan",
                                    data: chartData.withdrawals,
                                    backgroundColor: "#2563eb",
                                    borderRadius: 4,
                                    maxBarThickness: 24,
                                },
                            ],
                        },
                        options: {
                            ...sharedOptions,
                            plugins: {
                                ...sharedOptions.plugins,
                                legend: {
                                    position: "bottom",
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 7,
                                        padding: 18,
                                    },
                                },
                            },
                        },
                    });
                }

                const activityCanvas = document.querySelector(
                    '[data-chart="activity"]',
                );
                if (activityCanvas) {
                    new Chart(activityCanvas, {
                        type: "line",
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: "Jumlah transaksi",
                                    data: chartData.transactions,
                                    borderColor: "#2563eb",
                                    backgroundColor: "rgba(37, 99, 235, 0.1)",
                                    fill: true,
                                    tension: 0.35,
                                    borderWidth: 2.5,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                },
                            ],
                        },
                        options: {
                            ...sharedOptions,
                            scales: {
                                ...sharedOptions.scales,
                                y: {
                                    ...sharedOptions.scales.y,
                                    ticks: { stepSize: 1, precision: 0 },
                                },
                            },
                            plugins: {
                                ...sharedOptions.plugins,
                                legend: { display: false },
                                tooltip: {
                                    ...sharedOptions.plugins.tooltip,
                                    callbacks: {
                                        label: (context) =>
                                            `${context.parsed.y} transaksi`,
                                    },
                                },
                            },
                        },
                    });
                }

                const distributionCanvas = document.querySelector(
                    '[data-chart="distribution"]',
                );
                if (distributionCanvas) {
                    new Chart(distributionCanvas, {
                        type: "doughnut",
                        data: {
                            labels: ["Setoran", "Penarikan"],
                            datasets: [
                                {
                                    data: chartData.transactionTypes,
                                    backgroundColor: ["#d9a928", "#2563eb"],
                                    borderColor: "#ffffff",
                                    borderWidth: 3,
                                    hoverOffset: 5,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: "72%",
                            animation: prefersReducedMotion
                                ? false
                                : { duration: 450 },
                            plugins: {
                                legend: {
                                    position: "bottom",
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 7,
                                        padding: 18,
                                    },
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) =>
                                            `${context.label}: ${new Intl.NumberFormat("id-ID").format(context.parsed)} transaksi`,
                                    },
                                },
                            },
                        },
                    });
                }

                document.querySelectorAll("[data-chart]").forEach((canvas) => {
                    markChartLoaded(canvas);
                });
            },
        )
        .catch(() => {
            document.querySelectorAll(".chart-surface").forEach((surface) => {
                surface.setAttribute("aria-busy", "false");
                surface.innerHTML =
                    '<p class="chart-error">Grafik tidak dapat dimuat saat ini.</p>';
            });
        });
}

const coinCanvas = document.querySelector("[data-savings-coin]");

if (coinCanvas) {
    const coinStage = coinCanvas.closest(".coin-stage");

    void import("three")
        .then(
            ({
                AmbientLight,
                CanvasTexture,
                CylinderGeometry,
                DirectionalLight,
                Group,
                Mesh,
                MeshBasicMaterial,
                MeshStandardMaterial,
                PerspectiveCamera,
                PlaneGeometry,
                QuadraticBezierCurve3,
                Scene,
                SRGBColorSpace,
                SphereGeometry,
                TorusGeometry,
                TubeGeometry,
                Vector3,
                WebGLRenderer,
            }) => {
                const scene = new Scene();
                const camera = new PerspectiveCamera(34, 1, 0.1, 20);
                const renderer = new WebGLRenderer({
                    canvas: coinCanvas,
                    alpha: true,
                    antialias: true,
                    preserveDrawingBuffer: true,
                });
                const coin = new Group();
                const gold = new MeshStandardMaterial({
                    color: "#d4a72c",
                    metalness: 0.72,
                    roughness: 0.28,
                });
                const edgeGold = new MeshStandardMaterial({
                    color: "#f3d777",
                    metalness: 0.68,
                    roughness: 0.26,
                });
                const sproutMaterial = new MeshStandardMaterial({
                    color: "#828b68",
                    metalness: 0.12,
                    roughness: 0.48,
                });

                renderer.setPixelRatio(
                    Math.min(window.devicePixelRatio || 1, 1.5),
                );
                renderer.setClearColor(0x000000, 0);
                camera.position.set(0, 0, 3.5);

                const body = new Mesh(
                    new CylinderGeometry(0.76, 0.76, 0.16, 48),
                    gold,
                );
                body.rotation.x = Math.PI / 2;
                coin.add(body);

                const rim = new Mesh(
                    new TorusGeometry(0.62, 0.045, 10, 48),
                    edgeGold,
                );
                rim.position.z = 0.09;
                coin.add(rim);

                const symbolCanvas = document.createElement("canvas");
                symbolCanvas.width = 256;
                symbolCanvas.height = 128;
                const symbolContext = symbolCanvas.getContext("2d");
                symbolContext.font = '700 88px "Avenir Next", sans-serif';
                symbolContext.textAlign = "center";
                symbolContext.textBaseline = "middle";
                symbolContext.fillStyle = "#fff1b3";
                symbolContext.fillText("Rp", 128, 65);
                const symbolTexture = new CanvasTexture(symbolCanvas);
                symbolTexture.colorSpace = SRGBColorSpace;
                const symbol = new Mesh(
                    new PlaneGeometry(0.72, 0.36),
                    new MeshBasicMaterial({
                        map: symbolTexture,
                        transparent: true,
                        toneMapped: false,
                    }),
                );
                symbol.position.set(-0.02, -0.02, 0.115);
                coin.add(symbol);

                const sproutCurve = new QuadraticBezierCurve3(
                    new Vector3(0.3, 0.34, 0.12),
                    new Vector3(0.42, 0.62, 0.15),
                    new Vector3(0.47, 0.92, 0.16),
                );
                coin.add(
                    new Mesh(
                        new TubeGeometry(sproutCurve, 20, 0.022, 8, false),
                        sproutMaterial,
                    ),
                );

                const leafGeometry = new SphereGeometry(1, 20, 14);
                const upperLeaf = new Mesh(leafGeometry, sproutMaterial);
                upperLeaf.position.set(0.55, 0.83, 0.16);
                upperLeaf.scale.set(0.13, 0.23, 0.045);
                upperLeaf.rotation.z = -0.48;
                coin.add(upperLeaf);

                const sideLeaf = new Mesh(leafGeometry, sproutMaterial);
                sideLeaf.position.set(0.34, 0.58, 0.16);
                sideLeaf.scale.set(0.19, 0.115, 0.045);
                sideLeaf.rotation.z = 0.48;
                coin.add(sideLeaf);
                scene.add(coin);

                scene.add(new AmbientLight(0xffffff, 1.5));
                const keyLight = new DirectionalLight(0xffffff, 2.4);
                keyLight.position.set(-2, 3, 4);
                scene.add(keyLight);

                const resize = () => {
                    const { width, height } = coinStage.getBoundingClientRect();
                    if (width === 0 || height === 0) {
                        return;
                    }

                    renderer.setSize(width, height, false);
                    camera.aspect = width / height;
                    camera.updateProjectionMatrix();
                    renderer.render(scene, camera);
                };

                new ResizeObserver(resize).observe(coinStage);
                resize();
                coinStage.classList.add("is-ready");

                let animationFrame = 0;
                let isVisible = false;
                const render = () => {
                    if (!isVisible) {
                        return;
                    }

                    if (!prefersReducedMotion) {
                        const animationTime = performance.now() / 1600;
                        coin.rotation.y = 0.22 + Math.sin(animationTime) * 0.12;
                        coin.rotation.z = Math.sin(animationTime * 0.7) * 0.035;
                        coin.position.y = Math.sin(animationTime * 0.8) * 0.035;
                    }

                    renderer.render(scene, camera);

                    if (!prefersReducedMotion) {
                        animationFrame = requestAnimationFrame(render);
                    }
                };

                new IntersectionObserver(([entry]) => {
                    isVisible = entry.isIntersecting;
                    cancelAnimationFrame(animationFrame);
                    if (isVisible) {
                        render();
                    }
                }).observe(coinStage);
            },
        )
        .catch(() => {
            coinCanvas.hidden = true;
        });
}

document.querySelectorAll("form").forEach((form) => {
    if (
        form.method.toUpperCase() !== "POST" ||
        form.classList.contains("deactivate-form")
    ) {
        return;
    }

    form.addEventListener("submit", () => {
        const submitButton = form.querySelector(
            'button[type="submit"], button:not([type])',
        );

        if (!submitButton || submitButton.disabled) {
            return;
        }

        submitButton.disabled = true;
        submitButton.setAttribute("aria-busy", "true");
        submitButton.classList.add("is-loading");
    });
});
