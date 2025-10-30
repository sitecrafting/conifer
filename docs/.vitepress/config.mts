import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: 'Conifer',
    description: 'Powerful abstractions for serious WordPress theme development',
    base: '/',
    themeConfig: {
        search: {
            provider: 'local'
        },
        editLink: {
            pattern: 'https://github.com/sitecrafting/conifer/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        },
        nav: [
            { text: 'Guide', link: '/what-is-conifer' },
        ],
        sidebar: {
            '/': [
                {
                    text: 'Getting Started',
                    items: [
                        { text: 'What is Conifer?', link: '/what-is-conifer' },
                        { text: 'Installation', link: '/installation' },
                        { text: 'Requirements', link: '/requirements' },
                        { text: 'Developer Setup', link: '/dev-setup' },
                    ],
                },
                {
                    text: 'Core Concepts',
                    items: [
                        { text: 'Basics', link: '/basics' },
                        { text: 'The Site Object', link: '/site' },
                        { text: 'Working with Posts', link: '/posts' },
                    ],
                },
                {
                    text: 'Features',
                    items: [
                        { text: 'Admin Functionality', link: '/admin' },
                        { text: 'AJAX Handlers', link: '/ajax-handlers' },
                        { text: 'Alerts', link: '/alerts' },
                        { text: 'Authorization', link: '/authorization' },
                        { text: 'Forms', link: '/forms' },
                        { text: 'Notifiers', link: '/notifiers' },
                        { text: 'Shortcodes', link: '/shortcodes' },
                        { text: 'Twig', link: '/twig' },
                    ],
                },
                {
                    text: 'Testing',
                    items: [{ text: 'Testing', link: '/testing' }],
                },
                {
                    text: 'Contributing',
                    items: [
                        { text: 'How to Contribute', link: '/how-to-contribute' },
                        { text: 'Governance', link: '/governance' },
                        { text: 'Code of Conduct', link: '/code-of-conduct' },
                    ],
                },
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
            {
                icon: 'x',
                link: 'https://x.com/sitecrafting',
            }
        ],
    },
})
