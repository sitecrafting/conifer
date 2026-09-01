import {defineConfig} from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: 'Conifer',
    description: 'Powerful abstractions for serious WordPress theme development',
    base: '/',
    head: [
        [
            'link', {rel: 'icon', href: '/favicon.ico'}
        ]
    ],
    themeConfig: {
        search: {
            provider: 'local'
        },
        editLink: {
            pattern: 'https://github.com/sitecrafting/conifer/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        },
        nav: [
            {text: 'Guide', link: '/what-is-conifer'},
        ],
        sidebar: {
            '/': [
                {
                    text: 'Getting Started',
                    items: [
                        {text: 'What is Conifer?', link: '/what-is-conifer'},
                        {text: 'Installation', link: '/installation'},
                        {text: 'Requirements', link: '/requirements'},
                        {text: 'Developer Setup', link: '/dev-setup'},
                    ],
                },
                {
                    text: 'Upgrading',
                    items: [
                        {text: 'Upgrading to V2', link: '/v2-upgrade'},
                    ],
                },
                {
                    text: 'Core Concepts',
                    items: [
                        {text: 'Basics', link: '/basics'},
                        {text: 'The Site Object', link: '/site'},
                        {text: 'Working with Posts', link: '/posts'},
                        {text: 'Working with Menus', link: '/menus'},
                        {text: 'Working with Menu Items', link: '/menu-items'},
                        {text: 'Working with Images', link: '/images'},
                    ],
                },
                {
                    text: 'Features',
                    items: [
                        {text: 'Admin Functionality', link: '/admin'},
                        {text: 'AJAX Handlers', link: '/ajax-handlers'},
                        {text: 'Alerts', link: '/alerts'},
                        {text: 'Authorization', link: '/authorization'},
                        {text: 'Forms', link: '/forms'},
                        {text: 'Notifiers', link: '/notifiers'},
                        {text: 'Shortcodes', link: '/shortcodes'},
                        {text: 'Twig Helpers', link: '/twig-helpers'},
                    ],
                },
                {
                    text: 'Testing',
                    items: [
                        {text: 'Testing', link: '/testing'}
                    ],
                },
                {
                    text: 'Contributing',
                    items: [
                        {text: 'How to Contribute', link: '/how-to-contribute'},
                        {text: 'Governance', link: '/governance'},
                        {text: 'Code of Conduct', link: '/code-of-conduct'},
                    ],
                },
                {
                    text: 'Changelog',
                    items: [
                        {text: '2018', link: '/changelog/2018'},
                        {text: '2019', link: '/changelog/2019'},
                        {text: '2020', link: '/changelog/2020'},   
                        {text: '2024', link: '/changelog/2024'},
                        {text: '2026', link: '/changelog/2026'},
                    ],
                }
            ],
        },
        // https://vitepress.dev/reference/default-theme-config#sociallinks
        socialLinks: [
            {
                icon: 'github',
                link: 'https://github.com/sitecrafting/conifer',
            },
            {
                icon: 'instagram',
                link: 'https://www.instagram.com/sitecrafting/',
            },
        ],
    },
})