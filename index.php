<?php
// v14.0 - FINAL CLEAN (Bilingual Read-Only)
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotel TV Launcher</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { font-family: 'Inter', sans-serif; overflow: hidden; }
.bg-launcher {
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    transition: background-image 0.4s ease-in-out;
}
.bg-overlay { background-color: rgba(0, 0, 0, 0.4); }

/* Kontainer Menu */
.menu-container-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.menu-scroll-container {
    display: flex;
    align-items: center;
    overflow: hidden;
    width: 1150px;
    height: 180px;
    justify-content: flex-start;
}
#main-menu-items {
    display: flex;
    flex-wrap: nowrap;
    white-space: nowrap;
    transition: transform 0.3s ease-in-out;
    align-items: center;
}
.nav-arrow { color: rgba(255,255,255,0.5); transition: all 0.2s ease; }
.nav-arrow.active { color: rgba(255,255,255,1); transform: scale(1.2); }

/* Ikon Menu Efek 3D */
.menu-item {
    transition: all 0.25s ease-in-out;
    width: 135px;
    height: 135px;
    margin: 0 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 22px;
    text-align: center;
    flex-shrink: 0;
    background: rgba(255,255,255,0.05);
    filter: brightness(0.85);
    border: none;
    box-shadow: 0 0 0 transparent;
    position: relative;
}
.menu-item::after {
    content: "";
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 8px;
    border-radius: 50%;
    background: radial-gradient(ellipse at center, rgba(255,255,255,0.25), transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.menu-item.focused {
    transform: translateY(-6px) scale(1.1);
    filter: brightness(1.25);
    background: radial-gradient(circle at top, rgba(255,255,255,0.15), rgba(0,0,0,0.4));
    box-shadow: 0 8px 25px rgba(0,0,0,0.55), 0 0 25px rgba(255,255,255,0.25) inset;
}
.menu-item.focused::after { opacity: 1; }
.menu-item-icon {
    width: 56px; height: 56px;
    object-fit: contain;
    margin-bottom: 6px;
    transform: scale(0.95);
    transition: transform 0.25s ease;
}
.menu-item.focused .menu-item-icon { transform: scale(1.12); }
.menu-item span { font-size: 0.9rem; transition: color 0.3s ease; }
.menu-item.focused span { color: #fff; }

.hidden { display: none; }
#boot-loading{
  position: fixed;
  inset: 0;
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.85);
}
.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px; height: 40px;
    animation: spin 1s linear infinite;
}
@keyframes spin { 0% {transform:rotate(0deg);} 100% {transform:rotate(360deg);} }

/* RunText */
.marquee-container {
    width: 100%;
    background-color: rgba(0,0,0,0.6);
    overflow: hidden;
    position: absolute;
    bottom: 0; left: 0;
    white-space: nowrap;
    box-sizing: border-box;
    padding: 6px 0;
}
.marquee-text {
    display: inline-block;
    color: white;
    font-size: 1rem;
    line-height: 1.5rem;
    animation: marquee 30s linear infinite;
    padding-left: 100%;
}
@keyframes marquee {
    0% {transform:translateX(0%);}
    100% {transform:translateX(-100%);}
}

.header-time { font-size:2.5rem; line-height:1.1; font-weight:700; }
.header-date { font-size:1rem; color:#d1d5db; }
.weather-icon {
    width: 2.5rem; 
    height: 2.5rem; 
    margin-right: 0.5rem;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
}
.guest-hello { font-size: 0.75rem; color:#d1d5db; }
.guest-name { font-size: 1.125rem; font-weight:600; }
.room-label { font-size: 0.75rem; color:#d1d5db; }
.room-number { font-size: 1.125rem; font-weight:600; }

.guest-avatar {
    width:48px; height:48px;
    border-radius:9999px;
    background-color:#374151;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
}
.guest-avatar svg { width:24px; height:24px; color:#9ca3af; }
.guest-info-wrapper { display:flex; align-items:center; gap:1rem; text-align:right; }
.guest-room-wrapper { padding-left:1rem; border-left:1px solid #4b5563; }

/* Register Screen */
.register-title { font-size:2rem; line-height:2.5rem; font-weight:700; color:#facc15; }
.register-desc { font-size:1.125rem; line-height:1.75rem; color:#d1d5db; }
.register-codebox { background:white; color:black; font-size:2rem; line-height:2.5rem; font-family:monospace; font-weight:700; padding:1.5rem; border-radius:.5rem; display:inline-block; min-width:12ch; text-align:center; }
.register-status { color:#facc15; font-size:1rem; line-height:1.5rem; margin-top:1.5rem; }
.register-footnote { color:#9ca3af; font-size:.75rem; line-height:1rem; margin-top:1.5rem; }

/* Hapus highlight kuning default */
*:focus { outline:none!important; border:none!important; box-shadow:none!important; }
.menu-item:focus-visible { outline:none!important; border:none!important; box-shadow:none!important; }
</style>
</head>
<body class="bg-gray-900 text-white h-screen w-screen overflow-hidden">

<div id="boot-loading">
</div>

<div id="launcher-container" class="relative h-full w-full bg-launcher hidden">
    <div class="absolute inset-0 bg-overlay"></div>
    <div class="relative z-10 h-full w-full flex flex-col p-8 md:p-12">
        <header class="flex justify-between items-start">
            <div class="flex items-center space-x-4">
                <img id="weather-icon-display" src="" alt="Cuaca" class="weather-icon hidden">
                <div>
                    <p class="header-time" id="time-display">--:--</p>
                    <p class="header-date" id="date-display">Loading...</p>
                </div>
                </div>
           
            <div class="guest-info-wrapper text-right">
                <div class="text-right"> <p class="guest-hello">Selamat Datang</p>
                    <p class="guest-name" id="guest-name-display">Fetching...</p>
                </div>
                <div class="guest-avatar"> <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="guest-room-wrapper text-right"> <p class="room-label">Room</p>
                    <p class="room-number" id="room-number-display">...</p>
                </div>
            </div>
           
        </header>

        <div class="flex-grow"></div>

        <footer class="menu-container-wrapper">
            <div id="nav-arrow-left" class="nav-arrow p-2">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
            </div>
            <div class="menu-scroll-container"><div id="main-menu-items"></div></div>
            <div id="nav-arrow-right" class="nav-arrow active p-2">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            </div>
        </footer>
    </div>

    <div class="marquee-container"><div class="marquee-text" id="marquee-text-content">Loading running text...</div></div>
    <div id="disabled-screen" class="absolute inset-0 bg-gray-900 z-50 hidden justify-center items-center p-10 text-center">
        <div>
            <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            <h1 class="text-3xl text-white font-bold mt-8">Launcher Disabled by Admin</h1>
            <p class="text-lg text-gray-400 mt-4">Please contact hotel staff to enable this TV.</p>
        </div>
    </div>
</div>

<div id="unregistered-screen" class="absolute inset-0 bg-gray-800 z-50 flex justify-center items-center p-10 text-center hidden">
    <div>
        <h1 class="register-title mb-4">Perangkat Belum Terdaftar</h1>
        <p class="register-desc mb-8">Hubungi admin hotel dan berikan kode unik di bawah ini:</p>
        <div class="register-codebox" id="unique-code-display"><div class="loader mx-auto"></div></div>
        <p class="register-status" id="polling-status">Menghasilkan kode unik...</p>
        <p class="register-footnote">Kode registrasi ini tidak akan berubah.<br>
            <a href="index.php?reset=true" class="text-yellow-400 underline" onclick="clearLocalStorage()">Klik di sini untuk reset (jika macet).</a>
        </p>
    </div>
</div>
<script>
const PAGE_ROUTES = {
  information: 'information.html?v=2',
  facilities: 'facilities.html?v=2',
  amenities: 'amenities.html?v=2',
  dining: 'dining.html?v=2'  
};

// == Kunci localStorage 
const STORAGE_REG_CODE_KEY = 'myRegistrationCode_v8_final';
const STORAGE_DEVICE_ID_KEY = 'myDeviceID_v8_final';
const STORAGE_GUEST_NAME_KEY = 'guest_name_v8_final';
const STORAGE_ROOM_NUM_KEY = 'room_number_v8_final';
const STORAGE_LANG_KEY = 'app_lang'; // KUNCI BAHASA

const POLLING_INTERVAL = 5000;
const GUEST_POLLING_INTERVAL = 10000;

let pollingIntervalId = null;
let currentDeviceID = null;
let menuItems = [];
let currentFocusIndex = 0;
let currentGuestName = "Fetching...";
const DEFAULT_FOCUS_KEY = 'facilities';

// === Elemen utama ===
const bootLoading = document.getElementById('boot-loading');
const launcherContainer = document.getElementById('launcher-container');
const disabledScreen = document.getElementById('disabled-screen');
const unregisteredScreen = document.getElementById('unregistered-screen');
const uniqueCodeDisplay = document.getElementById('unique-code-display');
const pollingStatus = document.getElementById('polling-status');
const marqueeText = document.getElementById('marquee-text-content');
const menuItemsContainer = document.getElementById('main-menu-items');
const menuScrollContainer = document.querySelector('.menu-scroll-container');
const guestNameEl = document.getElementById('guest-name-display');
const roomNumberEl = document.getElementById('room-number-display');
const timeEl = document.getElementById('time-display');
const dateEl = document.getElementById('date-display');
const weatherIconEl = document.getElementById('weather-icon-display'); 
const navArrowLeft = document.getElementById('nav-arrow-left');
const navArrowRight = document.getElementById('nav-arrow-right');

// === Fungsi dasar ===
function clearLocalStorage() {
  try {
    localStorage.removeItem(STORAGE_REG_CODE_KEY);
    localStorage.removeItem(STORAGE_DEVICE_ID_KEY);
    localStorage.removeItem(STORAGE_GUEST_NAME_KEY);
    localStorage.removeItem(STORAGE_ROOM_NUM_KEY);
    localStorage.removeItem(STORAGE_LANG_KEY);
  } catch (e) {}
}

function getStableRegistrationCode() {
  const params = new URLSearchParams(window.location.search);
  if (params.has('reset')) {
    clearLocalStorage();
    window.location.replace(window.location.pathname);
    return null;
  }

  let code = localStorage.getItem(STORAGE_REG_CODE_KEY);
  if (code) {
    uniqueCodeDisplay.textContent = code;
    return code;
  }

  const saved = localStorage.getItem(STORAGE_DEVICE_ID_KEY);
  if (saved) {
    uniqueCodeDisplay.textContent = saved;
    return saved;
  }

  code = 'TV-' + Math.random().toString(36).substr(2, 6).toUpperCase();
  localStorage.setItem(STORAGE_REG_CODE_KEY, code);
  uniqueCodeDisplay.textContent = code;
  return code;
}

async function checkRegistrationStatus(code) {
  if (!code) return;
  pollingStatus.textContent = 'Menunggu admin mengaktifkan perangkat...';
  try {
    const res = await fetch(`./api.php?action=checkRegistration&device_id=${code}&_=${Date.now()}`);
    const data = await res.json();
    if (data.status === 'success' && data.is_registered) {
      pollingStatus.textContent = 'Terdaftar! Memuat launcher...';
      stopPolling();
      currentDeviceID = code;
      localStorage.removeItem(STORAGE_REG_CODE_KEY);
      localStorage.setItem(STORAGE_DEVICE_ID_KEY, currentDeviceID);
      loadLauncherData(currentDeviceID, getCurrentLang()); 
    }
  } catch (err) {
    pollingStatus.textContent = 'Gagal menghubungi server.';
  }
}

function startPolling(code) {
  if (!code) return;
  checkRegistrationStatus(code);
  pollingIntervalId = setInterval(() => checkRegistrationStatus(code), POLLING_INTERVAL);
}
function stopPolling() { clearInterval(pollingIntervalId); }

async function pollGuestInfo() {
    if (!currentDeviceID) return; 

    try {
        const res = await fetch(`./api.php?action=getGuestInfo&device_id=${currentDeviceID}&_=${Date.now()}`);
        const guestData = await res.json();
        
        if (guestData.status === 'success') {
            const newGuestName = guestData.data.guest_name;
            if (newGuestName !== currentGuestName) {
                currentGuestName = newGuestName;
                guestNameEl.textContent = newGuestName;
                localStorage.setItem(STORAGE_GUEST_NAME_KEY, newGuestName);
            }
        }
    } catch (err) {
        console.warn("Polling nama tamu gagal.", err);
    }
}

// === FUNGSI BAHASA (READ ONLY) ===
function getCurrentLang() {
    return localStorage.getItem(STORAGE_LANG_KEY) || 'id'; 
}

function applyLanguageText(lang) {
    // Update teks UI statis
    document.querySelector('.guest-hello').textContent = (lang === 'id') ? 'Selamat Datang' : 'Welcome';
    document.querySelector('.room-label').textContent = (lang === 'id') ? 'Kamar' : 'Room';
}

function getWeatherIcon(iconCode) {
    return `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
}

// === Pemuatan Data Launcher ===
async function loadLauncherData(deviceID, lang) {
  try {
    const statusRes = await fetch(`./api.php?action=getStatus&_=${Date.now()}`);
    const statusData = await statusRes.json();
    if (statusData.status === 'success' && !statusData.is_launcher_enabled) {
      launcherContainer.classList.add('hidden');
      disabledScreen.classList.remove('hidden');
      unregisteredScreen.classList.add('hidden');
      bootLoading.style.display = 'none';
      if (window.AndroidBridge && typeof window.AndroidBridge.hideLoadingScreen === 'function') {
        window.AndroidBridge.hideLoadingScreen();
      }
      bootLoading.style.display = 'none';
      return;
    }

    // Ambil data dengan parameter bahasa yang dipilih di halaman Greeting
    const [guestRes, marqueeRes, appsRes, weatherRes] = await Promise.all([
      fetch(`./api.php?action=getGuestInfo&device_id=${deviceID}&_=${Date.now()}`),
      fetch(`./api.php?action=getMarqueeText&lang=${lang}&_=${Date.now()}`),
      fetch(`./api.php?action=getAppVisibility&lang=${lang}&_=${Date.now()}`),
      fetch(`./api.php?action=getWeather&lang=${lang}&_=${Date.now()}`) 
    ]);

    // Info tamu
    const guestData = guestRes.ok ? await guestRes.json() : {};
    if (guestData.status === 'success') {
      const guestName = guestData.data.guest_name;
      const roomNumber = guestData.data.room_number;
      guestNameEl.textContent = guestName;
      roomNumberEl.textContent = roomNumber;
      currentGuestName = guestName; 
      localStorage.setItem(STORAGE_GUEST_NAME_KEY, guestName);
      localStorage.setItem(STORAGE_ROOM_NUM_KEY, roomNumber);
    }
    
    // Terapkan teks bahasa
    applyLanguageText(lang);

    // Runtext
    const marqueeData = marqueeRes.ok ? await marqueeRes.json() : {};
    if (marqueeData.status === 'success') marqueeText.textContent = marqueeData.text;

    // Menu ikon
    const appsData = appsRes.ok ? await appsRes.json() : {};
    if (appsData.status === 'success' && appsData.apps) {
      buildMainMenu(appsData.apps);
      const idx = appsData.apps.findIndex(a => a.app_key === DEFAULT_FOCUS_KEY);
      if (idx !== -1) currentFocusIndex = idx;
    }
    
    // Data Cuaca
    const weatherData = weatherRes.ok ? await weatherRes.json() : {};
    let weatherInfo = null;
    if (weatherData.status === 'success') {
        weatherInfo = weatherData.data; 
        weatherIconEl.src = getWeatherIcon(weatherData.data.icon);
        weatherIconEl.classList.remove('hidden');
    }

    // Tampilkan launcher
    launcherContainer.classList.remove('hidden');
    unregisteredScreen.classList.add('hidden');
    disabledScreen.classList.add('hidden');
    bootLoading.style.display = 'none';

    setFocus(currentFocusIndex);
    setTimeout(() => {
      if (menuItems[currentFocusIndex]) menuItems[currentFocusIndex].classList.add('focused');
      scrollMenu();
    }, 100);

    document.addEventListener('keydown', handleKeyDown);
    
    updateClock(weatherInfo);
    setInterval(() => updateClock(weatherInfo), 1000); 
    setInterval(pollGuestInfo, GUEST_POLLING_INTERVAL);
    loadDynamicBackground();

    if (window.AndroidBridge && typeof window.AndroidBridge.hideLoadingScreen === 'function') {
        window.AndroidBridge.hideLoadingScreen();
    }

  } catch (err) {
    bootLoading.style.display = 'none';
    unregisteredScreen.classList.remove('hidden');
    unregisteredScreen.classList.remove('hidden');
    pollingStatus.textContent = `Error: ${err.message}`;
    if (window.AndroidBridge && typeof window.AndroidBridge.hideLoadingScreen === 'function') {
        window.AndroidBridge.hideLoadingScreen();
    }
  }
}

// === Panggil background dinamis dari API ===
async function loadDynamicBackground() {
  try {
    const res = await fetch(`./api.php?action=getHomeBackground&_=${Date.now()}`);
    const data = await res.json();
    const bgUrl = (data.status === 'success' && data.background_url) ? data.background_url : 'img/hotel3.png'; 
    const launcherBg = document.querySelector('.bg-launcher');
    if (launcherBg) launcherBg.style.backgroundImage = `url('${bgUrl}')`;
  } catch (err) {
     const launcherBg = document.querySelector('.bg-launcher');
     if (launcherBg) launcherBg.style.backgroundImage = "url('img/hotel3.png')";
  }
}

// === Fungsi menu & navigasi ===
function buildMainMenu(apps) {
  menuItemsContainer.innerHTML = '';
  apps.forEach((app, i) => {
    const el = document.createElement('div');
    el.className = 'menu-item';
    el.tabIndex = 0; 
    el.dataset.index = i;
    el.dataset.page = PAGE_ROUTES[app.app_key] || '';
    el.dataset.pkg = app.android_package || '';
    el.dataset.label = app.app_name || 'App';
    el.innerHTML = `<img src="${app.icon_path}" class="menu-item-icon" alt=""><span>${app.app_name}</span>`;
    
    el.addEventListener('click', () => { handleItemClick(el); });
    el.addEventListener('focus', () => { setFocus(i); scrollMenu(); });
    menuItemsContainer.appendChild(el);
  });
  menuItems = document.querySelectorAll('.menu-item');
  updateArrowVisibility();
}

function handleItemClick(item) {
    if (!item) return;
    if (item.dataset.page) {
        if (window.AndroidBridge && typeof window.AndroidBridge.hideLoadingScreen === 'function') {
            window.AndroidBridge.hideLoadingScreen();
        }
        window.location.href = item.dataset.page;
    }
    else if (item.dataset.pkg) {
        launchNativeApp(item.dataset.pkg, item.dataset.label);
    }
}

function handleKeyDown(e) {
  const blocked = ['MetaLeft','MetaRight','Home','Escape','Back','F10'];
  if (blocked.includes(e.key)) { e.preventDefault(); return; }
  
  const activeEl = document.activeElement;
  if (activeEl && activeEl.classList.contains('menu-item')) {
      const currentIndex = parseInt(activeEl.dataset.index || '0');
      switch (e.key) {
        case 'ArrowLeft':
          e.preventDefault();
          if (currentIndex > 0) menuItems[currentIndex - 1].focus();
          break;
        case 'ArrowRight':
          e.preventDefault();
          if (currentIndex < menuItems.length - 1) menuItems[currentIndex + 1].focus();
          break;
        case 'Enter':
          e.preventDefault();
          handleItemClick(activeEl);
          break;
      }
  } else {
      if (e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Enter') {
          if (menuItems.length > 0) menuItems[currentFocusIndex].focus();
      }
  }
}

function setFocus(i) {
  if (!menuItems.length) return;
  if (menuItems[currentFocusIndex]) menuItems[currentFocusIndex].classList.remove('focused');
  currentFocusIndex = Math.max(0, Math.min(i, menuItems.length - 1));
  if (menuItems[currentFocusIndex]) menuItems[currentFocusIndex].classList.add('focused');
  updateArrowVisibility();
}

function scrollMenu() {
  const focused = menuItems[currentFocusIndex];
  if (!focused) return;
  const containerWidth = menuScrollContainer.offsetWidth;
  const itemWidth = focused.offsetWidth + 8;
  const scrollAmt = (currentFocusIndex * itemWidth) - (containerWidth / 2) + (itemWidth / 2);
  const maxScroll = menuItemsContainer.scrollWidth - containerWidth;
  menuItemsContainer.style.transform = `translateX(-${Math.max(0, Math.min(scrollAmt, maxScroll))}px)`;
}

function updateArrowVisibility() {
  if (currentFocusIndex === 0) navArrowLeft.classList.remove('active');
  else navArrowLeft.classList.add('active');
  if (currentFocusIndex === menuItems.length - 1) navArrowRight.classList.remove('active');
  else navArrowRight.classList.add('active');
}

function launchNativeApp(pkg, label) {
  try {
    if (window.AndroidBridge && typeof window.AndroidBridge.launchApp === 'function') {
      window.AndroidBridge.launchApp(pkg);
    } else {
        console.warn('Simulating launch: ' + label);
    }
  } catch (e) { console.error('Failed to launch ' + label, e); }
}

function updateClock(weatherData) {
  const now = new Date();
  const lang = getCurrentLang();
  
  timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
  const dateString = now.toLocaleDateString(lang === 'en' ? 'en-US' : 'id-ID', { 
      weekday:'long', day:'numeric', month:'long', year:'numeric' 
  });
  
  let weatherString = '28&deg;C | Cerah';
  if (weatherData) weatherString = `${weatherData.temp}&deg;C | ${weatherData.description}`;
  dateEl.innerHTML = `${weatherString} | ${dateString}`;
}

// === Boot ===
document.addEventListener('DOMContentLoaded', () => {
  const id = localStorage.getItem(STORAGE_DEVICE_ID_KEY);
  const initialLang = getCurrentLang();

  bootLoading.style.display = 'flex';
  launcherContainer.classList.add('hidden');
  unregisteredScreen.classList.add('hidden');
  disabledScreen.classList.add('hidden');
  bootLoading.style.display = 'none';

  if (id) {
    currentDeviceID = id;
    loadLauncherData(currentDeviceID, initialLang);
  } else {
    bootLoading.style.display = 'none';
    const code = getStableRegistrationCode();
    unregisteredScreen.classList.remove('hidden');
    if (code) startPolling(code);

    if (window.AndroidBridge && typeof window.AndroidBridge.hideLoadingScreen === 'function') {
      window.AndroidBridge.hideLoadingScreen();
    }
  }
});

// Placeholder Bridge
if (typeof window.AndroidBridge === 'undefined') {
    window.AndroidBridge = {
        launchApp: (pkg) => console.log(`Simulate launchApp: ${pkg}`),
        hideLoadingScreen: () => console.log("Simulate hideLoadingScreen")
    };
}
</script>
</body>
</html>