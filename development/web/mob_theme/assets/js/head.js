/*
Template Name: Performance Pad - Responsive Tailwind Admin Dashboard
Version: 1.0
Author: Opiam Analytics
File: Head js
*/

class Config {
    adjustLayout() {
        const html = document.getElementsByTagName("html")[0];
        html.setAttribute("data-sidebar-view", window.innerWidth <= 1024 ? "mobile" : "default");
    }

    initWindowSize() {
        window.addEventListener('resize', () => this.adjustLayout());
    }

    init() {
        this.adjustLayout();
        this.initWindowSize();
    }
}

new Config().init();
