import { useContext, useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../api/axios';
import { AuthContext } from '../context/AuthContext';
import ValidationMessage from '../components/ValidationMessage';

export default function LoginPage() {
  const auth = useContext(AuthContext);
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: '', password: '' });
  const [errors, setErrors] = useState({});

  const validate = () => {
    const fieldErrors = {};
    if (!form.email) fieldErrors.email = 'Email is required.';
    if (!form.password) fieldErrors.password = 'Password is required.';
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
      const response = await api.post('/api/auth/login', form);
      auth.login(response.data.data);
      navigate('/bookings');
    } catch (error) {
      setErrors({ form: 'Login failed. Please check your credentials.' });
    }
  };

  return (
    <div className="max-w-md mx-auto bg-white p-6 rounded-xl shadow-sm">
      <h1 className="text-2xl font-semibold">Login</h1>
      {errors.form && <div className="text-red-600 mt-4">{errors.form}</div>}
      <form className="space-y-4 mt-6" onSubmit={submit}>
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
        <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded-md">Login</button>
      </form>
      <p className="mt-4 text-sm text-slate-600">
        Need an account? <Link className="text-blue-600" to="/register">Register</Link>
      </p>
      <p className="mt-2 text-sm text-slate-600">
        <Link className="text-blue-600" to="/forgot-password">Forgot password?</Link>
      </p>
    </div>
  );
}
