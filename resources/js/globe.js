import * as THREE from 'three';
import ThreeGlobe from 'three-globe';
import WebGL from 'three/addons/capabilities/WebGL.js';
import earthNight from '../../public/world/earth-night.jpg';
import earthTopology from '../../public/world/earth-topology.png';

if (! WebGL.isWebGL2Available()) {
	const warning = THREE.WEBGL.getWebGLErrorMessage();
	throw new Error(warning);
}

const node = document.getElementById('globe-visualization');
const points = JSON.parse(node.dataset.points);

const colorInterpolator = t => `rgba(255, 100, 50, ${ 1 - t })`;

const Globe = new ThreeGlobe()
	.globeImageUrl(earthNight)
	.bumpImageUrl(earthTopology)
	.ringsData(points)
	.ringColor(() => colorInterpolator)
	.ringMaxRadius(() => 5)
	.ringPropagationSpeed(() => 1)
	.ringRepeatPeriod(() => 2000)
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

function setSize() {
	const width = node.clientWidth;
	const height = node.clientHeight;
	
	camera.position.z = width > 1000 ? 130 : 110;
	camera.aspect = width / height;
	camera.updateProjectionMatrix();
	
	renderer.setSize(width, height);
}

setSize();
window.addEventListener('resize', setSize, false);
node.appendChild(renderer.domElement);

function ease(k) {
	return .5 * (Math.sin((k - .5) * Math.PI) + 1);
}

function interpolate(from, to, tick, ticks) {
	const k = Math.max(0, Math.min(1, tick / (ticks - 1)));
	const easing = ease(k);
	return from + (to - from) * easing;
}

const SECONDS_BETWEEN_POINTS = 4;
const SECONDS_TO_PAUSE = 4;

let mode = 'move';

let point_index = 0;
let frame = 0;
let frames_per_point = (60 * SECONDS_BETWEEN_POINTS); // Assume 60 fps at first
let last_timestamp = 0;
let fps = 0;

function move(frame)
{
	const prev = 0 === point_index ? points[points.length - 1] : points[point_index - 1];
	const next = points[point_index];
	
	const x = THREE.MathUtils.degToRad(interpolate(prev.lat, next.lat, frame, frames_per_point));
	const y = THREE.MathUtils.degToRad(-1 * interpolate(prev.lng, next.lng, frame, frames_per_point));
	
	Globe.rotation.set(x, y, 0, 'XYZ');
	
	const normalized = (frame - (frames_per_point / 2)) / (frames_per_point / 2);
	camera.position.z = -15 * (normalized * normalized) + 130;
	
	if (frame >= frames_per_point) {
		frames_per_point = Math.ceil(fps * SECONDS_BETWEEN_POINTS);
		point_index = points.length <= (point_index + 1) ? 0 : point_index + 1;
		return true;
	}
	
	return false;
}

function pause(frame)
{
	camera.position.z = 115 - (frame / 100);
	
	if (frame >= (frames_per_point / 2)) {
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
	
	renderer.render(scene, camera);
	requestAnimationFrame(animate);
})();
