import { useContext } from 'react';
import { Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

export default function Navbar() {
  const auth = useContext(AuthContext);

  return (
    <header className="bg-white border-b border-slate-200">
      <div className="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <Link className="text-xl font-semibold text-slate-900" to="/">BookEase</Link>
        <nav className="flex items-center gap-4 text-slate-700">
          <Link to="/">Properties</Link>
          {auth?.token ? (
            <>
              <Link to="/bookings">My Bookings</Link>
              {auth.isAdmin() && <Link to="/admin">Admin</Link>}
              <button onClick={auth.logout} className="text-slate-600 hover:text-slate-900">Logout</button>
            </>
          ) : (
            <>
              <Link to="/login">Login</Link>
              <Link to="/register">Register</Link>
            </>
          )}
        </nav>
      </div>
    </header>
  );
}
