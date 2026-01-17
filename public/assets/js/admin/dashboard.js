/**
 * Admin Dashboard - Statistics loader
 */

document.addEventListener('DOMContentLoaded', function() {
	loadDashboardStats();
});


/**
 * Load dashboard statistics from API
 */
function loadDashboardStats() {
	fetch('/Projekt/public/api/dashboard-stats.php')
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(data => {
			if (data.success) {
				updateDashboardUI(data.data);
			} else {
				console.error('API Error:', data.error);
				showErrorMessage('Błąd podczas pobierania danych');
			}
		})
		.catch(error => {
			console.error('Fetch Error:', error);
			showErrorMessage('Błąd połączenia z serwerem');
		});
}

/**
 * Update dashboard UI with fetched data
 */
function updateDashboardUI(data) {
	// Update Events
	updateTileValue(1, data.events_total);
	updateTileValue(2, data.events_month);
	updateTileValue(3, data.events_today);

	// Update Participants
	updateTileValue(4, data.participants_total);
	updateTileValue(5, data.participants_month);
	updateTileValue(6, data.participants_today);

	// Draw line charts for yearly series (tile 1 and 4)
	if (data.events_year && data.events_year.length > 0) {
		drawLineChartInTile(1, data.events_year, '#27ae60');
	}
	if (data.participants_year && data.participants_year.length > 0) {
		drawLineChartInTile(4, data.participants_year, '#3498db');
	}

	// Draw line charts for current month (tile 2 and 5)
	if (data.events_month_series && data.events_month_series.length > 0) {
		drawLineChartInTile(2, data.events_month_series, '#2ecc71');
	}
	if (data.participants_month_series && data.participants_month_series.length > 0) {
		drawLineChartInTile(5, data.participants_month_series, '#3498db');
	}

	// Draw two-bar comparison for today vs yesterday in tiles 3 and 6
	if (typeof data.events_today !== 'undefined' && typeof data.events_yesterday !== 'undefined') {
		renderTwoBarsInTile(3, data.events_today, data.events_yesterday, '#16a085');
	}
	if (typeof data.participants_today !== 'undefined' && typeof data.participants_yesterday !== 'undefined') {
		renderTwoBarsInTile(6, data.participants_today, data.participants_yesterday, '#2980b9');
	}
}

/**
 * Update tile value
 */
function updateTileValue(tileNumber, value) {
	const tiles = document.querySelectorAll('.dashboard-tile');
	if (tiles[tileNumber - 1]) {
		const valueElement = tiles[tileNumber - 1].querySelector('.dashboard-tile__value');
		if (valueElement) {
			// Format number with thousands separator
			valueElement.textContent = formatNumber(value);
		}
	}
}

/**
 * Format number with thousands separator
 */
function formatNumber(num) {
	return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Draw a simple SVG line chart inside a tile's .dashboard-tile__chart
 * series: [{ period: 'YYYY-MM', count: N }, ...]
 */
function drawLineChartInTile(tileNumber, series, strokeColor) {
	const tiles = document.querySelectorAll('.dashboard-tile');
	const tile = tiles[tileNumber - 1];
	if (!tile) return;

	const chartContainer = tile.querySelector('.dashboard-tile__chart') || tile.querySelector('.dashboard-tile__chart-center');
	if (!chartContainer) return;

	// Remove existing svg if present
	const existing = chartContainer.querySelector('svg.admin-line-chart');
	if (existing) existing.remove();

	// Prepare data
	const values = series.map(s => parseInt(s.count || 0));
	const width = Math.max(chartContainer.clientWidth, 200);

	// Clear container and create vertical wrapper so labels appear under the SVG
	chartContainer.innerHTML = '';
	const wrapper = document.createElement('div');
	wrapper.className = 'admin-line-chart-wrapper';
	wrapper.style.display = 'flex';
	wrapper.style.flexDirection = 'column';
	wrapper.style.alignItems = 'stretch';

	// append wrapper early so we can measure available height for SVG
	chartContainer.appendChild(wrapper);

	// compute SVG height based on actual wrapper height and reserve space for labels
	const totalH = wrapper.clientHeight || 56;
	const labelArea = 18; // px reserved for label row
	const padding = 6;
	const svgHeight = Math.max(48, totalH - labelArea);

	// Use 1rem as step per event for vertical increments
	const remPx = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
	const max = Math.max(...values, 1);

	// Create SVG
	const svgNS = 'http://www.w3.org/2000/svg';
	const svg = document.createElementNS(svgNS, 'svg');
	svg.setAttribute('class', 'admin-line-chart');
	svg.setAttribute('width', '100%');
	svg.setAttribute('height', svgHeight);
	svg.setAttribute('viewBox', `0 0 ${width} ${svgHeight}`);

	// Points
	const stepX = (width - padding * 2) / Math.max(values.length - 1, 1);
	// max drawable height inside svg (between paddings)
	const drawableH = Math.max(svgHeight - padding * 2, 8);
	const points = values.map((v, i) => {
		const x = padding + i * stepX;
		// each event raises point by 1rem; cap at drawableH
		const rise = Math.min((Number(v) || 0) * remPx, drawableH);
		const y = padding + (drawableH - rise);
		return `${x},${y}`;
	}).join(' ');

	// Polyline
	const poly = document.createElementNS(svgNS, 'polyline');
	poly.setAttribute('points', points);
	poly.setAttribute('fill', 'none');
	poly.setAttribute('stroke', strokeColor);
	poly.setAttribute('stroke-width', '2');
	poly.setAttribute('stroke-linejoin', 'round');
	poly.setAttribute('stroke-linecap', 'round');
	svg.appendChild(poly);

	// Simple filled area (light)
	const area = document.createElementNS(svgNS, 'polygon');
	const areaPoints = values.map((v, i) => {
		const x = padding + i * stepX;
		const rise = Math.min((Number(v) || 0) * remPx, drawableH);
		const y = padding + (drawableH - rise);
		return `${x},${y}`;
	}).join(' ');
	const lastX = padding + (values.length - 1) * stepX;
	const areaFull = `${areaPoints} ${lastX},${svgHeight - padding} ${padding},${svgHeight - padding}`;
	area.setAttribute('points', areaFull);
	area.setAttribute('fill', strokeColor);
	area.setAttribute('opacity', '0.08');
	svg.insertBefore(area, poly);

	wrapper.appendChild(svg);

	// Create labels (numbers) under chart
	const labelsRow = document.createElement('div');
	labelsRow.className = 'admin-line-chart-labels';
	labelsRow.style.display = 'flex';
	labelsRow.style.justifyContent = 'space-between';
	labelsRow.style.gap = '0.25rem';
	labelsRow.style.marginTop = '0.35rem';
	labelsRow.style.fontSize = '0.75rem';
	labelsRow.style.color = '#666';
	labelsRow.style.flexWrap = 'nowrap';

	// Determine label text: if series items have 'date' use day number, if 'period' use month number
	series.forEach((s, i) => {
		const lbl = document.createElement('div');
		lbl.style.flex = '1 1 0';
		lbl.style.textAlign = 'center';
		lbl.style.overflow = 'hidden';
		lbl.style.whiteSpace = 'nowrap';
		lbl.style.textOverflow = 'ellipsis';
		let text = '';
		if (s.date) {
			// YYYY-MM-DD -> day
			const parts = (s.date + '').split('-');
			text = parts.length === 3 ? String(Number(parts[2])) : s.date;
		} else if (s.period) {
			// YYYY-MM -> month number
			const parts = (s.period + '').split('-');
			text = parts.length === 2 ? String(Number(parts[1])) : s.period;
		} else {
			text = i + 1;
		}
		lbl.textContent = text;
		labelsRow.appendChild(lbl);
	});

	wrapper.appendChild(labelsRow);
	chartContainer.appendChild(wrapper);
}

/**
 * Render two vertical bars (yesterday vs today) inside a tile
 */
function renderTwoBarsInTile(tileNumber, todayCount, yesterdayCount, color) {
	const tiles = document.querySelectorAll('.dashboard-tile');
	const tile = tiles[tileNumber - 1];
	if (!tile) return;

	// prefer the bottom-aligned chart container so bars stay at the bottom
	const chartContainer = tile.querySelector('.dashboard-tile__chart') || tile.querySelector('.dashboard-tile__chart-center');
	if (!chartContainer) return;

	// Clear existing content
	chartContainer.innerHTML = '';

	const wrapper = document.createElement('div');
	wrapper.style.display = 'flex';
	wrapper.style.alignItems = 'flex-end';
	wrapper.style.justifyContent = 'center';
	wrapper.style.gap = '10rem';
	// make wrapper fill available vertical space so alignItems:flex-end anchors bars to bottom
	wrapper.style.height = '100%';
	wrapper.style.padding = '0.25rem';
	wrapper.style.width = '100%';

	// append early so we can read clientHeight for pixel calculations
	chartContainer.appendChild(wrapper);

	const max = Math.max(Number(todayCount) || 0, Number(yesterdayCount) || 0, 1);

	// create a centered group to keep the two bars together in the middle
	const group = document.createElement('div');
	group.style.display = 'flex';
	group.style.gap = '1rem';
	group.style.alignItems = 'flex-end';
	group.style.justifyContent = 'center';
	group.style.width = 'auto';

	function createBar(label, value) {
		const col = document.createElement('div');
		col.style.display = 'flex';
		col.style.flexDirection = 'column';
		col.style.alignItems = 'center';
		col.style.justifyContent = 'flex-end';
		col.style.minWidth = '10rem';

		// compute available height in px for bars (subtract space for labels)
		const totalH = wrapper.clientHeight || 80;
		const labelArea = 32; // approximate space for label + value
		const availableH = Math.max(totalH - labelArea, 28);
		// Use discrete steps: each participant increases height by 1rem (converted to px)
		const remPx = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
		const stepPx = remPx; // 1rem
		// height = value * 1rem, capped to availableH
		const rawPx = Math.round((Number(value) || 0) * stepPx);
		const barPx = Math.min(rawPx, availableH);

		const bar = document.createElement('div');
		// make bars wider (4x visually compared to previous small bars)
		bar.style.width = '7rem';
		bar.style.height = barPx + 'px';
		bar.style.background = color;
		bar.style.borderRadius = '6px 6px 0 0';
        bar.style.paddingBottom = '0.5rem';

		const lbl = document.createElement('div');
		lbl.style.marginTop = '0.35rem';
		lbl.style.fontSize = '0.75rem';
		lbl.textContent = label;

		const val = document.createElement('div');
		val.style.fontSize = '1rem';
		val.style.marginTop = '0.15rem';
		val.textContent = formatNumber(value);

		col.appendChild(bar);
		col.appendChild(lbl);
		col.appendChild(val);
		return col;
	}

	group.appendChild(createBar('Wczoraj', yesterdayCount));
	group.appendChild(createBar('Dziś', todayCount));
	wrapper.appendChild(group);
}

/**
 * Show error message
 */
function showErrorMessage(message) {
	// You can implement a toast notification or alert here
	console.error(message);
	
	// Optional: Show user-friendly error in UI
	const errorDiv = document.createElement('div');
	errorDiv.className = 'admin-error-toast';
	errorDiv.textContent = message;
	errorDiv.style.cssText = 'position: fixed; top: 1rem; right: 1rem; background: #e74c3c; color: white; padding: 1rem; border-radius: 0.5rem; z-index: 9999;';
	document.body.appendChild(errorDiv);
	
	setTimeout(() => {
		errorDiv.remove();
	}, 3000);
}
