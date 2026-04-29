<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Visit Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP BAR ── */
        #topbar {
            background: #1a1a2e;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        #topbar .title {
            font-size: 16px;
            font-weight: 700;
            color: #e2e8f0;
            margin-right: 10px;
            white-space: nowrap;
        }

        #topbar input,
        #topbar select {
            padding: 7px 12px;
            border: 1px solid #334155;
            border-radius: 8px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 13px;
            outline: none;
        }

        #topbar input:focus,
        #topbar select:focus {
            border-color: #3b82f6;
        }

        #topbar button {
            padding: 7px 18px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        #topbar button:hover {
            background: #2563eb;
        }

        #topbar .back-btn {
            background: #334155;
            text-decoration: none;
            color: white;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        #topbar .back-btn:hover {
            background: #475569;
        }

        /* ── SUMMARY BAR ── */
        #summary-bar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 20px;
            display: flex;
            gap: 30px;
            align-items: center;
            z-index: 999;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .stat-icon.blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .stat-icon.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .stat-icon.orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .stat-icon.purple {
            background: #f3e8ff;
            color: #9333ea;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
            line-height: 1;
        }

        .stat-value {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.4;
        }

        #emp-name-display {
            margin-left: auto;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        /* ── MAIN LAYOUT ── */
        #main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ── MAP ── */
        #map {
            flex: 1;
            z-index: 1;
        }

        /* ── TIMELINE SIDEBAR ── */
        #timeline {
            width: 320px;
            background: white;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.3s;
        }

        #timeline.collapsed {
            width: 0;
        }

        #timeline-header {
            padding: 14px 16px;
            background: #1a1a2e;
            color: white;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        #timeline-list {
            overflow-y: auto;
            flex: 1;
            padding: 10px 0;
        }

        .timeline-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
            position: relative;
        }

        .timeline-item:hover {
            background: #f8fafc;
        }

        .timeline-item.active {
            background: #eff6ff;
            border-left: 3px solid #3b82f6;
        }

        .t-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .t-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .t-num.start {
            background: #16a34a;
        }

        .t-num.end {
            background: #2563eb;
        }

        .t-num.order {
            background: #16a34a;
        }

        .t-num.noorder {
            background: #dc2626;
        }

        .t-outlet {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .t-time {
            font-size: 11px;
            color: #64748b;
            margin-left: 30px;
        }

        .t-pcs {
            font-size: 11px;
            color: #16a34a;
            font-weight: 600;
            margin-left: 30px;
        }

        .t-distance {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 16px 0 46px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* ── TOGGLE TIMELINE BUTTON ── */
        #toggle-timeline {
            position: absolute;
            right: 330px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px 0 0 8px;
            padding: 10px 6px;
            cursor: pointer;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
            transition: right 0.3s;
            color: #475569;
            font-size: 16px;
        }

        #toggle-timeline.collapsed {
            right: 10px;
            border-radius: 8px;
        }

        /* ── LOADING OVERLAY ── */
        #loading {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
        }

        #loading.show {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── EMPTY STATE ── */
        #empty-state {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 500;
            background: white;
            padding: 30px 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        #empty-state i {
            font-size: 40px;
            color: #cbd5e1;
            margin-bottom: 10px;
            display: block;
        }

        #empty-state p {
            color: #64748b;
            font-size: 14px;
        }

        /* ── LEGEND ── */
        #legend {
            position: absolute;
            bottom: 30px;
            left: 15px;
            z-index: 1000;
            background: white;
            border-radius: 10px;
            padding: 10px 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            font-size: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 5px;
            color: #374151;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* scrollbar */
        #timeline-list::-webkit-scrollbar {
            width: 4px;
        }

        #timeline-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        #timeline-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .emp-option {
            user-select: none;
            -webkit-user-select: none;
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <div id="topbar">
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <span class="title"><i class="bi bi-map"></i> Visit Map</span>

        <!-- Employee Autocomplete -->
        <div style="position:relative">
            <div style="display:flex;align-items:center;background:#0f172a;border:1px solid #334155;border-radius:8px;overflow:visible">
                <i class="bi bi-person" style="color:#94a3b8;padding:0 8px;font-size:14px"></i>
                <input
                    type="text"
                    id="empSearch"
                    placeholder="Type employee name..."
                    autocomplete="off"
                    style="width:200px;background:transparent;border:none;padding:7px 8px 7px 0;color:#e2e8f0;font-size:13px;outline:none"
                    oninput="searchEmployees(this.value)"
                    onkeydown="handleKey(event)"
                    onfocus="searchEmployees(this.value)" />
                <!-- Hidden field stores actual emp_id -->
                <input type="hidden" id="empId">
                <!-- Clear button -->
                <span id="clearEmp" onclick="clearEmployee()"
                    style="display:none;cursor:pointer;padding:0 8px;color:#94a3b8;font-size:12px">
                    <i class="bi bi-x-circle-fill"></i>
                </span>
            </div>

            <!-- Dropdown -->
            <div id="empDropdown" style="
            display:none;
            position:absolute;
            top:calc(100% + 4px);
            left:0;
            width:280px;
            background:white;
            border-radius:10px;
            box-shadow:0 8px 24px rgba(0,0,0,0.15);
            border:1px solid #e2e8f0;
            z-index:9999;
            max-height:260px;
            overflow-y:auto;
        "></div>
        </div>

        <!-- Date Navigation -->
        <div style="display:flex;align-items:center;gap:4px">
            <button onclick="changeDate(-1)" title="Previous Day" style="
        width:32px;height:32px;border:none;
        background:#334155;color:#e2e8f0;
        border-radius:8px;cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        font-size:16px;transition:background 0.2s"
                onmouseover="this.style.background='#475569'"
                onmouseout="this.style.background='#334155'">
                ‹
            </button>

            <input type="date" id="date"
                onkeydown="handleDateKey(event)"
                style="
            width:150px;padding:7px 10px;
            border:1px solid #334155;
            border-radius:8px;
            background:#e2e8f0;
            color:#1e293b;
            font-size:13px;
            font-weight:600;
            outline:none;
            cursor:pointer;
        ">

            <button onclick="changeDate(1)" title="Next Day" style="
        width:32px;height:32px;border:none;
        background:#334155;color:#e2e8f0;
        border-radius:8px;cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        font-size:16px;transition:background 0.2s"
                onmouseover="this.style.background='#475569'"
                onmouseout="this.style.background='#334155'">
                ›
            </button>
        </div>
        <button id="searchBtn" onclick="loadMap()">
            <i class="bi bi-search"></i> Search
        </button>
        <button onclick="resetMap()" style="background:#475569">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </button>
    </div>

    <!-- SUMMARY BAR -->
    <div id="summary-bar">
        <div class="stat-item">
            <div class="stat-icon blue"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
                <div class="stat-label">Total Visits</div>
                <div class="stat-value" id="s-visits">—</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon green"><i class="bi bi-signpost-2"></i></div>
            <div>
                <div class="stat-label">Distance</div>
                <div class="stat-value" id="s-km">—</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon orange"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="stat-label">Total PCS</div>
                <div class="stat-value" id="s-pcs">—</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon purple"><i class="bi bi-shop"></i></div>
            <div>
                <div class="stat-label">Orders Taken</div>
                <div class="stat-value" id="s-orders">—</div>
            </div>
        </div>
        <div id="emp-name-display"></div>
    </div>

    <!-- MAIN -->
    <div id="main" style="position:relative">

        <!-- MAP -->
        <div id="map"></div>

        <!-- LOADING -->
        <div id="loading">
            <div class="spinner"></div>
            <span style="color:#475569;font-size:14px">Loading visit data...</span>
        </div>

        <!-- EMPTY STATE -->
        <div id="empty-state">
            <i class="bi bi-calendar-x"></i>
            <p>No visits found for this employee on the selected date.</p>
        </div>

        <!-- LEGEND -->
        <div id="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#16a34a"></div> Start / Order Taken
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#dc2626"></div> No Order
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#2563eb"></div> Last Visit
            </div>
        </div>

        <!-- TOGGLE TIMELINE -->
        <div id="toggle-timeline" onclick="toggleTimeline()" title="Toggle Timeline">
            <i class="bi bi-layout-sidebar-reverse" id="toggle-icon"></i>
        </div>

        <!-- TIMELINE SIDEBAR -->
        <div id="timeline">
            <div id="timeline-header">
                <span><i class="bi bi-clock-history"></i> Visit Timeline</span>
                <span id="timeline-count" style="font-size:12px;opacity:0.7"></span>
            </div>
            <div id="timeline-list"></div>
        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Pass PHP data to JS once on page load -->

    <script>
        const ALL_EMPLOYEES = <?php echo json_encode($employees); ?>;
    </script>
    <script>
        // ── MAP INIT ──
        const map = L.map('map').setView([11.3410, 77.7172], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        let markersLayer = [];
        let polylineLayer = null;
        let arrowMarkers = [];
        let timelineCollapsed = false;

        // ── CUSTOM MARKER ICONS ──
        function makeIcon(color) {
            const colors = {
                green: {
                    bg: '#16a34a',
                    border: '#14532d'
                },
                red: {
                    bg: '#dc2626',
                    border: '#7f1d1d'
                },
                blue: {
                    bg: '#2563eb',
                    border: '#1e3a8a'
                },
            };
            const c = colors[color] || colors.red;
            return L.divIcon({
                className: '',
                html: `<div style="
                width:14px;height:14px;
                background:${c.bg};
                border:2px solid ${c.border};
                border-radius:50%;
                box-shadow:0 1px 4px rgba(0,0,0,0.3)
            "></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7],
                popupAnchor: [0, -10],
            });
        }

        function makeNumberIcon(num, color) {
            const colors = {
                green: '#16a34a',
                red: '#dc2626',
                blue: '#2563eb',
            };
            const bg = colors[color] || colors.red;
            return L.divIcon({
                className: '',
                html: `<div style="
                width:26px;height:26px;
                background:${bg};
                color:white;
                border-radius:50%;
                display:flex;align-items:center;justify-content:center;
                font-size:11px;font-weight:700;
                border:2px solid white;
                box-shadow:0 2px 6px rgba(0,0,0,0.3);
                font-family:Arial,sans-serif;
            ">${num}</div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13],
                popupAnchor: [0, -14],
            });
        }

        // ── LOAD MAP ──
        async function loadMap() {
            const empId = document.getElementById('empId').value.trim();
            const date = document.getElementById('date').value;

            if (!empId || !date) {
                alert('Please enter Employee ID and select a Date.');
                return;
            }

            showLoading(true);
            clearMap();
            document.getElementById('empty-state').style.display = 'none';

            try {
                const res = await fetch(`/admin/employee-visit-map-web/${empId}?date=${date}`);
                const data = await res.json();
                const visits = data.visits;

                if (!visits || visits.length === 0) {
                    document.getElementById('empty-state').style.display = 'block';
                    resetStats();
                    showLoading(false);
                    return;
                }

                // ── UPDATE SUMMARY ──
                document.getElementById('s-visits').textContent = data.total_visits;
                document.getElementById('s-km').textContent = data.total_km + ' km';
                document.getElementById('s-pcs').textContent = data.total_pcs;

                const ordersCount = visits.filter(v => v.pcs > 0).length;
                document.getElementById('s-orders').textContent = ordersCount;

                if (visits[0]?.emp_name) {
                    document.getElementById('emp-name-display').innerHTML =
                        '<i class=\"bi bi-person\"></i> ' + visits[0].emp_name;
                }

                const latlngs = [];

                visits.forEach((visit, index) => {
                    const pos = [visit.lat, visit.lng];
                    latlngs.push(pos);

                    // Decide color
                    let color = 'red';
                    if (index === 0) color = 'green';
                    else if (index === visits.length - 1) color = 'blue';
                    else if (visit.pcs > 0) color = 'green';

                    const icon = makeNumberIcon(index + 1, color);

                    // Format time
                    const timeStr = visit.time ?
                        new Date(visit.time).toLocaleTimeString('en-IN', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }) :
                        '';

                    const selfieHtml = visit.selfie_url ?
                        `<div style="margin-top:8px">
                        <img 
                            src="${visit.selfie_url}" 
                            alt="Store selfie"
                            style="width:100%;border-radius:6px;cursor:pointer;max-height:140px;object-fit:cover"
                            onclick="window.open('${visit.selfie_url}', '_blank')"
                        />
                        <div style="font-size:10px;color:#94a3b8;margin-top:3px;text-align:center">
                            Tap to view full image
                        </div>
                    </div>` :
                        `<div style="margin-top:8px;font-size:11px;color:#94a3b8;text-align:center;padding:6px;background:#f8fafc;border-radius:6px">
                        No selfie captured
                    </div>`;

                    const popupContent = `
                        <div style="font-family:Arial,sans-serif;min-width:200px">
                            <div style="font-weight:700;font-size:14px;margin-bottom:6px;color:#1e293b">${visit.name}</div>
                            <div style="font-size:12px;color:#64748b;margin-bottom:4px"><i class="bi bi-clock"></i> ${timeStr}</div>
                            <div style="font-size:12px;margin-top:6px;display:flex;gap:8px;flex-wrap:wrap">
                                <span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:10px;font-weight:600">
                                    L: ${visit.leggings ?? 0}
                                </span>
                                <span style="background:#dbeafe;color:#2563eb;padding:2px 8px;border-radius:10px;font-weight:600">
                                    NL: ${visit.non_leggings ?? 0}
                                </span>
                                <span style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:10px;font-weight:600">
                                    IW: ${visit.innerwear ?? 0}
                                </span>
                            </div>
                            <div style="margin-top:8px;font-size:13px;font-weight:700;color:#1e293b">
                                Total PCS: ${visit.pcs}
                            </div>
                            ${selfieHtml}
                        </div>`;

                    const marker = L.marker(pos, {
                            icon
                        })
                        .addTo(map)
                        .bindPopup(popupContent, {
                            maxWidth: 260
                        });

                    markersLayer.push({
                        marker,
                        index
                    });
                });

                // ── ROUTE POLYLINE ──
                if (latlngs.length > 1) {
                    polylineLayer = L.polyline(latlngs, {
                        color: '#000000',
                        weight: 2.5,
                        opacity: 0.8,
                        dashArray: '6, 4',
                    }).addTo(map);

                    // ── DIRECTION ARROWS ──
                    addDirectionArrows(latlngs);
                }

                // ── FIT MAP ──
                const group = L.featureGroup(markersLayer.map(m => m.marker));
                map.fitBounds(group.getBounds().pad(0.15));

                // ── BUILD TIMELINE ──
                buildTimeline(visits);

            } catch (err) {
                console.error(err);
                alert('Error loading map data. Please try again.');
            }

            showLoading(false);
        }

        // ── DIRECTION ARROWS ──
        function addDirectionArrows(latlngs) {
            for (let i = 0; i < latlngs.length - 1; i++) {
                const start = latlngs[i];
                const end = latlngs[i + 1];

                // midpoint
                const midLat = (start[0] + end[0]) / 2;
                const midLng = (start[1] + end[1]) / 2;

                const bearing = getBearing(start, end);

                const arrowIcon = L.divIcon({
                    className: '',
                    html: `<div style="
                    width:0;height:0;
                    border-left:5px solid transparent;
                    border-right:5px solid transparent;
                    border-bottom:10px solid #ff0000;
                    transform:rotate(${bearing}deg);
                    opacity:0.8;
                "></div>`,
                    iconSize: [10, 10],
                    iconAnchor: [5, 5],
                });

                const arrow = L.marker([midLat, midLng], {
                    icon: arrowIcon,
                    interactive: false
                }).addTo(map);
                arrowMarkers.push(arrow);
            }
        }

        function getBearing(start, end) {
            const lat1 = start[0] * Math.PI / 180;
            const lat2 = end[0] * Math.PI / 180;
            const dLng = (end[1] - start[1]) * Math.PI / 180;
            const y = Math.sin(dLng) * Math.cos(lat2);
            const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
            return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
        }

        // ── BUILD TIMELINE ──
        function buildTimeline(visits) {
            const list = document.getElementById('timeline-list');
            list.innerHTML = '';
            document.getElementById('timeline-count').textContent = visits.length + ' stops';

            visits.forEach((visit, index) => {
                const isFirst = index === 0;
                const isLast = index === visits.length - 1;
                const hasOrder = visit.pcs > 0;

                let numClass = hasOrder ? 'order' : 'noorder';
                if (isFirst) numClass = 'start';
                if (isLast) numClass = 'end';

                const timeStr = visit.time ?
                    new Date(visit.time).toLocaleTimeString('en-IN', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }) :
                    '';

                const item = document.createElement('div');
                item.className = 'timeline-item';
                item.id = `tl-${index}`;

                item.innerHTML = `
                <div class="t-header">
                    <div class="t-num ${numClass}">${index + 1}</div>
                    <div class="t-outlet" title="${visit.name}">${visit.name}</div>
                </div>
                <div class="t-time"><i class="bi bi-clock"></i> ${timeStr}</div>
                ${visit.pcs > 0 ? `<div class="t-pcs"><i class="bi bi-box"></i> PCS: ${visit.pcs} (L:${visit.leggings ?? 0} NL:${visit.non_leggings ?? 0} IW:${visit.innerwear ?? 0})</div>` : '<div class="t-pcs" style="color:#dc2626"><i class="bi bi-x-circle"></i> No order</div>'}
            `;

                // Click → zoom to marker
                item.addEventListener('click', () => {
                    const m = markersLayer[index];
                    if (m) {
                        map.setView(m.marker.getLatLng(), 16, {
                            animate: true
                        });
                        m.marker.openPopup();
                        document.querySelectorAll('.timeline-item').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                    }
                });

                list.appendChild(item);

                // Distance to next
                if (index < visits.length - 1) {
                    const next = visits[index + 1];
                    const dist = haversineKm(visit.lat, visit.lng, next.lat, next.lng);
                    const t1 = new Date(visit.time);
                    const t2 = new Date(next.time);
                    const mins = Math.round((t2 - t1) / 60000);
                    const timeLabel = mins >= 60 ?
                        `${Math.floor(mins/60)}h ${mins%60}m` :
                        `${mins}m`;

                    const connector = document.createElement('div');
                    connector.className = 't-distance';
                    connector.innerHTML = `<i class="bi bi-arrow-down" style="font-size:10px"></i> ${dist.toFixed(2)} km · ${timeLabel}`;
                    list.appendChild(connector);
                }
            });
        }

        function haversineKm(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // ── HELPERS ──
        function clearMap() {
            markersLayer.forEach(m => map.removeLayer(m.marker));
            markersLayer = [];
            arrowMarkers.forEach(a => map.removeLayer(a));
            arrowMarkers = [];
            if (polylineLayer) {
                map.removeLayer(polylineLayer);
                polylineLayer = null;
            }
            document.getElementById('timeline-list').innerHTML = '';
            document.getElementById('timeline-count').textContent = '';
            document.getElementById('emp-name-display').textContent = '';
        }

        function resetStats() {
            ['s-visits', 's-km', 's-pcs', 's-orders'].forEach(id => {
                document.getElementById(id).textContent = '—';
            });
        }

        function resetMap() {
            clearMap();
            resetStats();
            document.getElementById('empty-state').style.display = 'none';
            document.getElementById('empId').value = '';
            document.getElementById('date').value = '';
            map.setView([11.3410, 77.7172], 10);
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('show', show);
        }

        function toggleTimeline() {
            timelineCollapsed = !timelineCollapsed;
            document.getElementById('timeline').classList.toggle('collapsed', timelineCollapsed);
            document.getElementById('toggle-timeline').classList.toggle('collapsed', timelineCollapsed);
            document.getElementById('toggle-icon').className = timelineCollapsed ?
                'bi bi-layout-sidebar' :
                'bi bi-layout-sidebar-reverse';
            // Recalculate toggle button position
            document.getElementById('toggle-timeline').style.right = timelineCollapsed ? '10px' : '330px';
            setTimeout(() => map.invalidateSize(), 350);
        }

        // ── AUTO FILL FROM URL PARAMS ──
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('emp_id')) {
                document.getElementById('empId').value = params.get('emp_id');
            }
            if (params.get('date')) {
                document.getElementById('date').value = params.get('date');
            }
            if (params.get('emp_id') && params.get('date')) {
                loadMap();
            }
        });
        // ── EMPLOYEE AUTOCOMPLETE (local, no API) ──
        let selectedEmpId = null;
        let dropdownIndex = -1;
        let dropdownItems = [];

        function searchEmployees(query) {
            selectedEmpId = null;
            document.getElementById('empId').value = '';
            document.getElementById('clearEmp').style.display = 'none';

            const q = query.toLowerCase().trim();

            dropdownItems = q === '' ?
                ALL_EMPLOYEES :
                ALL_EMPLOYEES.filter(emp =>
                    emp.emp_name.toLowerCase().includes(q) ||
                    emp.emp_id.toString().includes(q)
                );

            // ✅ Auto highlight first item
            dropdownIndex = dropdownItems.length > 0 ? 0 : -1;

            showDropdown(dropdownItems, query);
        }

        function showDropdown(employees, query) {
            const dd = document.getElementById('empDropdown');
            dropdownItems = employees;

            if (!employees.length) {
                dd.innerHTML = `
            <div style="padding:14px 16px;color:#94a3b8;font-size:13px;text-align:center">
                <i class="bi bi-person-x" style="font-size:20px;display:block;margin-bottom:4px"></i>
                No employees found
            </div>`;
                dd.style.display = 'block';
                return;
            }

            dd.innerHTML = employees.map((emp, i) => {
                const q = query.toLowerCase().trim();
                const regex = q ? new RegExp(`(${q})`, 'gi') : null;
                const highlighted = regex ?
                    emp.emp_name.replace(regex, '<mark style="background:#dbeafe;color:#1e40af;border-radius:2px;padding:0 2px">$1</mark>') :
                    emp.emp_name;

                return `
            <div class="emp-option" data-index="${i}"
                onmousedown="selectEmployee('${emp.emp_id}', '${emp.emp_name.replace(/'/g,"\\'")}', ${i})"
                onmouseover="highlightItem(${i})"
                style="padding:10px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9;
                       display:flex;align-items:center;gap:10px;user-select:none;-webkit-user-select:none">
                <div style="width:32px;height:32px;background:#dbeafe;border-radius:8px;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-fill" style="color:#2563eb;font-size:14px"></i>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1e293b">${highlighted}</div>
                    <div style="font-size:11px;color:#94a3b8">ID: ${emp.emp_id}</div>
                </div>
            </div>`;
            }).join('');

            dd.style.display = 'block';

            // ✅ Highlight first item by default
            highlightItem(0);
        }

        function selectEmployee(empId, empName, index) {
            selectedEmpId = empId;
            document.getElementById('empId').value = empId;
            document.getElementById('empSearch').value = empName;
            document.getElementById('clearEmp').style.display = 'inline';
            hideDropdown();
        }

        function highlightItem(index) {
            dropdownIndex = index;
            document.querySelectorAll('.emp-option').forEach((el, i) => {
                el.style.background = i === index ? '#f0f7ff' : '';
            });
        }

        function handleKey(e) {
            const dd = document.getElementById('empDropdown');

            // ── ENTER KEY FLOW ──
            if (e.key === 'Enter') {
                e.preventDefault();

                // Step 1 — Dropdown open → select highlighted employee
                if (dd.style.display !== 'none' && dropdownIndex >= 0 && dropdownItems.length > 0) {
                    const emp = dropdownItems[dropdownIndex];
                    selectEmployee(emp.emp_id, emp.emp_name, dropdownIndex);
                    // Move focus to date input
                    setTimeout(() => document.getElementById('date').focus(), 50);
                    return;
                }

                // Step 2 — Employee already selected, focus date
                if (document.getElementById('empId').value) {
                    document.getElementById('date').focus();
                    return;
                }
            }

            // ── ARROW KEYS ──
            if (dd.style.display === 'none') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                dropdownIndex = Math.min(dropdownIndex + 1, dropdownItems.length - 1);
                highlightItem(dropdownIndex);
                scrollDropdownToItem(dropdownIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                dropdownIndex = Math.max(dropdownIndex - 1, 0);
                highlightItem(dropdownIndex);
                scrollDropdownToItem(dropdownIndex);
            } else if (e.key === 'Escape') {
                hideDropdown();
            }
        }

        // ✅ Scroll dropdown to keep highlighted item visible
        function scrollDropdownToItem(index) {
            const dd = document.getElementById('empDropdown');
            const items = dd.querySelectorAll('.emp-option');
            if (items[index]) {
                items[index].scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        function clearEmployee() {
            selectedEmpId = null;
            document.getElementById('empId').value = '';
            document.getElementById('empSearch').value = '';
            document.getElementById('clearEmp').style.display = 'none';
            document.getElementById('empSearch').focus();
        }

        function hideDropdown() {
            document.getElementById('empDropdown').style.display = 'none';
            dropdownIndex = -1;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#empDropdown') && e.target.id !== 'empSearch') {
                hideDropdown();
            }
        });
        // ── DATE HELPERS ──
        // Set today as default on page load
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;
        });

        // Previous / Next day buttons
        function changeDate(days) {
            const input = document.getElementById('date');
            const current = input.value ? new Date(input.value) : new Date();
            current.setDate(current.getDate() + days);
            input.value = current.toISOString().split('T')[0];
        }

        // Date input Enter → focus search button
        function handleDateKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchBtn').focus();
            }
        }
    </script>

</body>

</html>