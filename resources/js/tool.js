Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-legacy-log-viewer',
      path: '/nova-legacy-log-viewer',
      component: require('./components/Tool').default,
    },
  ])
})
