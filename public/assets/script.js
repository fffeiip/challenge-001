document.addEventListener('DOMContentLoaded', function() {
  const params = new URLSearchParams(window.location.search);
  const currentSort = params.get('sort');
  const currentDir = params.get('dir') || 'asc';

  // 1) On load: mark the active column
  if (currentSort) {
    const th = document.querySelector(`th.sortable[data-column="${currentSort}"]`);
    if (th) th.classList.add(currentDir);
  }

  // 2) Intercept clicks on sort links
  document.querySelectorAll('th.sortable').forEach(th => {
    const link = th.querySelector('a');
    if (!link) return;
    link.addEventListener('click', e => {
      e.preventDefault();
      const u = new URL(link.href, window.location.origin);
      const p = u.searchParams;
      const col = p.get('sort');
      // flip direction
      const dir = (p.get('dir') === 'desc') ? 'asc' : 'desc';
      p.set('sort', col);
      p.set('dir', dir);
      p.set('page', 1);
      window.location.search = p.toString();
    });
  });

  // Confirmation for delete forms
  document.querySelectorAll('form[action*="/delete"]').forEach(form => {
    form.addEventListener('submit', e => {
      if (! window.confirm('Are you sure you want to delete this record?')) {
        e.preventDefault();
      }
    });
  });
});
