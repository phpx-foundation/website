import * as THREE from 'three';
import ThreeGlobe from 'three-globe';
import { TrackballControls } from 'three/examples/jsm/controls/TrackballControls.js';

const node = document.getElementById('globe-visualization');
const data = JSON.parse(node.dataset.points);

const Globe = new ThreeGlobe()
	.globeImageUrl('https://unpkg.com/three-globe/example/img/earth-night.jpg')
	.bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
	.pointsData(data)
	.pointAltitude('size');

// Setup renderer
const renderer = new THREE.WebGLRenderer();
renderer.setSize(node.clientWidth, node.clientHeight);
node.appendChild(renderer.domElement);

// Setup scene
const scene = new THREE.Scene();
scene.add(Globe);
scene.add(new THREE.AmbientLight(0xcccccc, Math.PI));
scene.add(new THREE.DirectionalLight(0xffffff, 0.6 * Math.PI));

// Setup camera
const camera = new THREE.PerspectiveCamera();
camera.aspect = node.clientWidth / node.clientHeight;
camera.updateProjectionMatrix();
camera.position.z = 180;

// Add camera controls
const tbControls = new TrackballControls(camera, renderer.domElement);
tbControls.minDistance = 101;
tbControls.rotateSpeed = 5;
tbControls.zoomSpeed = 0.8;

// Position globe
Globe.rotation.y = THREE.MathUtils.degToRad(data[0].lng);
Globe.rotation.x = THREE.MathUtils.degToRad(-1 * data[0].lat);

// Kick-off renderer
(function animate() { // IIFE
	// Frame cycle
	tbControls.update();
	Globe.rotation.y += 0.001;
	renderer.render(scene, camera);
	requestAnimationFrame(animate);
})();
