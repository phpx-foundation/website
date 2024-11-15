import * as THREE from 'three';
import ThreeGlobe from 'three-globe';
import { TrackballControls } from 'three/examples/jsm/controls/TrackballControls.js';

const node = document.getElementById('globe-visualization');
const data = JSON.parse(node.dataset.points);

const colorInterpolator = t => `rgba(255, 100, 50, ${ 1 - t })`;

const Globe = new ThreeGlobe()
	.globeImageUrl('https://unpkg.com/three-globe/example/img/earth-night.jpg')
	.bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
	.ringsData(data)
	.ringColor(() => colorInterpolator)
	.ringMaxRadius(() => 5)
	.ringPropagationSpeed(() => 1)
	.ringRepeatPeriod(() => 2000)
	.labelsData(data)
	.labelSize(() => 0.8)
	.labelDotRadius(() => 0.1)
	.labelColor(() => 'white')
	.labelText('name');

// Setup renderer
const renderer = new THREE.WebGLRenderer({ alpha: true });

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

// Add camera controls
const tbControls = new TrackballControls(camera, renderer.domElement);
tbControls.minDistance = 101;
tbControls.rotateSpeed = 1;
tbControls.zoomSpeed = 0.2;

function setSize() {
	const width = node.clientWidth;
	const height = node.clientHeight;
	
	camera.position.z = width > 1000 ? 150 : 120;
	camera.aspect = width / height;
	camera.updateProjectionMatrix();
	
	renderer.setSize(width, height);
}

setSize();
window.addEventListener('resize', setSize, false);
node.appendChild(renderer.domElement);

let last_coords = [null, null];

function show(lat, lng) {
	const x = THREE.MathUtils.degToRad(lat);
	const y = THREE.MathUtils.degToRad(-lng);
	
	last_coords = [x, y];
	
	Globe.rotation.set(x, y, 0, 'XYZ');
}

// Kick-off renderer
// let centered = false;
(function animate() {
	show(data[0].lat, data[0].lng);
	// Globe.rotation.y += 0.001
	
	tbControls.update();
	renderer.render(scene, camera);
	requestAnimationFrame(animate);
})();

// setTimeout(() => console.log(Globe.rotation), 1000);
