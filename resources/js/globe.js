import * as THREE from 'three';
import ThreeGlobe from 'three-globe';
import WebGL from 'three/addons/capabilities/WebGL.js';
// import earthNight from '../../public/world/earth-night.jpg';
// import earthNightHighRes from '../../public/world/earth-night-hires.jpg';
import earthNightCustom from '../../public/world/earth-night-custom.jpg';
import earthTopology from '../../public/world/earth-topology.png';

if (! WebGL.isWebGL2Available()) {
	const warning = THREE.WEBGL.getWebGLErrorMessage();
	throw new Error(warning);
}

const node = document.getElementById('globe-visualization');
// const debug_node = document.getElementById('debug');
const points = JSON.parse(node.dataset.points);

const colorInterpolator = t => `rgba(255, 210, 210, ${ 1 - t })`;
// const colorInterpolator = t => `rgba(83, 116, 255, ${ 1 - t })`; // php.net

// const is_fast_connection = ('connection' in navigator && navigator.connection && 'downlink' in navigator.connection && navigator.connection.downlink >= 10);

function random_number(min, max) {
	return Math.random() * (max - min) + min;
}

const Globe = new ThreeGlobe()
	// .globeImageUrl(is_fast_connection ? earthNightHighRes : earthNight)
	.globeImageUrl(earthNightCustom)
	.bumpImageUrl(earthTopology)
	.ringsData(points)
	.ringColor(() => colorInterpolator)
	.ringMaxRadius(() => random_number(2.5, 4))
	.ringPropagationSpeed(() => 1)
	.ringRepeatPeriod(() => random_number(1800, 2200))
	.labelsData(points)
	.labelSize(() => 0.25)
	.labelDotRadius(() => 0.05)
	.labelColor(() => 'white')
	.labelText('name');

// Setup renderer
const renderer = new THREE.WebGLRenderer({
	alpha: true,
	antialias: true,
});
renderer.setPixelRatio(window.devicePixelRatio);

// Setup scene
const scene = new THREE.Scene();
scene.add(Globe);
scene.add(new THREE.AmbientLight(0xcccccc, Math.PI));
scene.add(new THREE.DirectionalLight(0xffffff, 0.6 * Math.PI));

// Setup camera
const camera = new THREE.PerspectiveCamera();
camera.aspect = node.clientWidth / node.clientHeight;
camera.updateProjectionMatrix();

// window.__debug__ = { Globe, renderer, scene, camera };

let default_z = 110;

function setSize() {
	const width = node.clientWidth;
	const height = node.clientHeight;
	
	default_z = width > 1000 ? 110 : 105;
	
	camera.position.z = default_z;
	camera.aspect = width / height;
	camera.updateProjectionMatrix();
	
	renderer.setSize(width, height);
}

setSize();
window.addEventListener('resize', setSize, false);
node.appendChild(renderer.domElement);

function getDistance(a, b) {
	const R = 6371; // Earth's radius in km
	const dLat = (b.lat - a.lat) * Math.PI / 180;
	const dLng = (b.lng - b.lng) * Math.PI / 180;
	
	const x = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
		Math.cos(a.lat * Math.PI / 180) * Math.cos(b.lat * Math.PI / 180) *
		Math.sin(dLng / 2) * Math.sin(dLng / 2);
	
	return 2 * R * Math.asin(Math.sqrt(x));
}

function ease(k) {
	return .5 * (Math.sin((k - .5) * Math.PI) + 1);
}

function easeInOut (frame, frames, min, max) {
	let eased = min + (max - min) * Math.sin(Math.PI * (frame / frames)) * Math.sin(Math.PI * (frame / frames));
	
	if (frame > (frames /2 )) {
		eased = Math.max(eased, default_z);
	}
	
	return eased;
}

function interpolate(from, to, tick, ticks) {
	let diff = to - from;
	
	if (Math.abs(diff) > 180) {
		diff = diff > 0 ? diff - 360 : diff + 360;
	}
	
	const k = Math.max(0, Math.min(1, tick / (ticks - 1)));
	const easing = ease(k);
	
	return from + diff * easing;
}

const SECONDS_BETWEEN_POINTS = 4;
const SECONDS_TO_PAUSE = 4;

let mode = 'move';

let point_index = 0;
let frame = 0;
let frames_per_point = (60 * SECONDS_BETWEEN_POINTS); // Assume 60 fps at first
let last_timestamp = 0;
let current_z = default_z;
let last_z = default_z;
let fps = 60;

function move(frame)
{
	const prev = 0 === point_index ? points[points.length - 1] : points[point_index - 1];
	const next = points[point_index];
	
	const x = THREE.MathUtils.degToRad(interpolate(prev.lat, next.lat, frame, frames_per_point));
	const y = THREE.MathUtils.degToRad(-1 * interpolate(prev.lng, next.lng, frame, frames_per_point));
	
	Globe.rotation.set(x, y, 0, 'XYZ');
	
	const distance = Math.max(0, Math.min(2500, getDistance(prev, next)));
	const distance_multiplier = distance / 2500;
	const height = 40 * distance_multiplier;
	
	camera.position.z = easeInOut(frame, frames_per_point, last_z, default_z + height);
	
	if (frame >= frames_per_point) {
		last_z = camera.position.z;
		frames_per_point = Math.ceil(fps * SECONDS_BETWEEN_POINTS);
		point_index = points.length <= (point_index + 1) ? 0 : point_index + 1;
		return true;
	}
	
	return false;
}

function pause(frame)
{
	camera.position.z = last_z - (frame / 100);
	
	if (frame >= (frames_per_point / 2)) {
		last_z = camera.position.z;
		return true;
	}
	
	return false;
}

// Kick-off renderer
(function animate(timestamp) {
	timestamp = timestamp * 0.001;
	
	if ('move' === mode && move(frame)) {
		mode = 'pause';
		frame = 0;
	} else if('pause' === mode && pause(frame)) {
		mode = 'move';
		frame = 0;
	}
	
	frame++;
	
	fps = 1 / (timestamp - last_timestamp);
	last_timestamp = timestamp;
	
	// if (debug_node) {
	// 	debug_node.innerText = `${ camera.position.z.toFixed(2) } (${ Math.round(fps) } fps)`;
	// }
	
	renderer.render(scene, camera);
	requestAnimationFrame(animate);
})();
