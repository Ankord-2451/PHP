import { useContext, useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../api/axios';
import { AuthContext } from '../context/AuthContext';
import ValidationMessage from '../components/ValidationMessage';

export default function RegisterPage() {
  const auth = useContext(AuthContext);
  const navigate = useNavigate();
  const [form, setForm] = useState({ name: '', email: '', password: '' });
  const [errors, setErrors] = useState({});

  const validate = () => {
    const fieldErrors = {};
    if (!form.name || form.name.length < 2) fieldErrors.name = 'Name must be at least 2 characters.';
    if (!form.email) fieldErrors.email = 'Email is required.';
    if (!form.password || form.password.length < 8) fieldErrors.password = 'Password must be at least 8 characters.';
    return fieldErrors;
  };

  const submit = async (event) => {
    event.preventDefault();
    const fieldErrors = validate();
    if (Object.keys(fieldErrors).length > 0) {
      setErrors(fieldErrors);
      return;
    }

    try {
      const response = await api.post('/api/auth/register', form);
      auth.login(response.data.data);
      navigate('/bookings');
    } catch (error) {
      setErrors({ form: 'Registration failed. Please review your information.' });
    }
  };

  return (
    <div className="max-w-md mx-auto bg-white p-6 rounded-xl shadow-sm">
      <h1 className="text-2xl font-semibold">Register</h1>
      {errors.form && <div className="text-red-600 mt-4">{errors.form}</div>}
      <form className="space-y-4 mt-6" onSubmit={submit}>
        <div>
          <label className="block text-sm font-medium">Name</label>
          <input
            type="text"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            className="mt-2 w-full"
          />
          <ValidationMessage message={errors.name} />
        </div>
        <div>
          <label className="block text-sm font-medium">Email</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            className="mt-2 w-full"
          />
          <ValidationMessage message={errors.email} />
        </div>
        <div>
          <label className="block text-sm font-medium">Password</label>
          <input
            type="password"
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
            className="mt-2 w-full"
          />
          <ValidationMessage message={errors.password} />
        </div>
        <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded-md">Register</button>
      </form>
      <p className="mt-4 text-sm text-slate-600">
        Already have an account? <Link className="text-blue-600" to="/login">Login</Link>
      </p>
    </div>
  );
}
