import api from '../api/axios';

export default function AdminUserTable({ users }) {
  const promoteUser = async (id) => {
    try {
      await api.put(`/api/admin/users/${id}/role`, { role: 'admin' });
      window.location.reload();
    } catch {
      alert('Unable to change role.');
    }
  };

  const deleteUser = async (id) => {
    if (!confirm('Delete this user?')) return;
    try {
      await api.delete(`/api/admin/users/${id}`);
      window.location.reload();
    } catch {
      alert('Unable to delete user.');
    }
  };

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm">
      <h2 className="text-xl font-semibold mb-4">User Management</h2>
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-slate-200">
              <th className="py-3">Name</th>
              <th className="py-3">Email</th>
              <th className="py-3">Role</th>
              <th className="py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user) => (
              <tr key={user.id} className="border-b border-slate-100">
                <td className="py-3">{user.name}</td>
                <td className="py-3">{user.email}</td>
                <td className="py-3">{user.role}</td>
                <td className="py-3 space-x-2">
                  {user.role !== 'admin' && (
                    <button onClick={() => promoteUser(user.id)} className="text-blue-600">Promote</button>
                  )}
                  <button onClick={() => deleteUser(user.id)} className="text-red-600">Delete</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
