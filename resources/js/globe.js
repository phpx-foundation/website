import * as THREE from 'three';
import ThreeGlobe from 'three-globe';
// import { TrackballControls } from 'three/examples/jsm/controls/TrackballControls.js';

const node = document.getElementById('globe-visualization');
const data = JSON.parse(node.dataset.points);

const colorInterpolator = t => `rgba(255, 100, 50, ${ 1 - t })`;

const Globe = new ThreeGlobe()
	.globeImageUrl('/world/earth-night.jpg')
	.bumpImageUrl('/world/earth-topology.png')
	.ringsData(data)
	.ringColor(() => colorInterpolator)
	.ringMaxRadius(() => 5)
	.ringPropagationSpeed(() => 1)
	.ringRepeatPeriod(() => 2000)
	.labelsData(data)
	.labelSize(() => 0.7)
	.labelDotRadius(() => 0.1)
	.labelColor(() => 'white')
	.labelText('name');

// Setup renderer
const renderer = new THREE.WebGLRenderer({
	// alpha: true,
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
camera.position.z = 150;

window.__debug__ = { Globe, renderer, scene, camera };

// Add camera controls
// const tbControls = new TrackballControls(camera, renderer.domElement);
// tbControls.minDistance = 101;
// tbControls.rotateSpeed = 1;
// tbControls.zoomSpeed = 0.2;

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

let seconds = 4; // seconds between animations
let point_index = 0;
let current_step = 0;
let tick_count = 100;
let last_timestamp = 0;
let fps = 0;

function tick(timestamp) {
	const prev = 0 === point_index ? data[data.length - 1] : data[point_index - 1];
	const next = data[point_index];
	
	const x = THREE.MathUtils.degToRad(interpolate(prev.lat, next.lat, current_step, tick_count));
	const y = THREE.MathUtils.degToRad(-1 * interpolate(prev.lng, next.lng, current_step, tick_count));
	
	Globe.rotation.set(x, y, 0, 'XYZ');
	
	current_step++;
	
	fps = 1 / (timestamp - last_timestamp);
	last_timestamp = timestamp;
	
	if (current_step >= tick_count) {
		tick_count = Math.ceil(fps * seconds);
		point_index = data.length <= (point_index + 1) ? 0 : point_index + 1;
		current_step = 0;
		// console.log({ item: data[point_index], fps });
	}
}

function ease(k) {
	return .5 * (Math.sin((k - .5) * Math.PI) + 1);
}

function interpolate(from, to, tick, ticks) {
	const k = Math.max(0, Math.min(1, tick / (ticks - 1)));
	const easing = ease(k);
	return from + (to - from) * easing;
}

// Kick-off renderer
(function animate(timestamp) {
	tick(timestamp * 0.001);
	
	// tbControls.update();
	renderer.render(scene, camera);
	requestAnimationFrame(animate);
})();
