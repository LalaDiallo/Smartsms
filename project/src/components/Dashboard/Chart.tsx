import React from 'react';
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell
} from 'recharts';

const lineData = [
  { name: 'Jan', sms: 4000, whatsapp: 2400, email: 1800 },
  { name: 'Fév', sms: 3000, whatsapp: 1398, email: 2200 },
  { name: 'Mar', sms: 2000, whatsapp: 9800, email: 2800 },
  { name: 'Avr', sms: 2780, whatsapp: 3908, email: 3200 },
  { name: 'Mai', sms: 1890, whatsapp: 4800, email: 2800 },
  { name: 'Jun', sms: 2390, whatsapp: 3800, email: 3400 },
];

const barData = [
  { name: 'Lun', delivered: 85, opened: 60, clicked: 35 },
  { name: 'Mar', delivered: 78, opened: 55, clicked: 42 },
  { name: 'Mer', delivered: 92, opened: 68, clicked: 38 },
  { name: 'Jeu', delivered: 88, opened: 72, clicked: 45 },
  { name: 'Ven', delivered: 95, opened: 78, clicked: 52 },
  { name: 'Sam', delivered: 82, opened: 58, clicked: 28 },
  { name: 'Dim', delivered: 75, opened: 48, clicked: 22 },
];

const pieData = [
  { name: 'SMS', value: 45, color: '#3B82F6' },
  { name: 'WhatsApp', value: 30, color: '#10B981' },
  { name: 'Email', value: 20, color: '#8B5CF6' },
  { name: 'Push', value: 5, color: '#F59E0B' },
];

interface ChartProps {
  type: 'line' | 'bar' | 'pie';
  title: string;
}

export function Chart({ type, title }: ChartProps) {
  const renderChart = () => {
    switch (type) {
      case 'line':
        return (
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={lineData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#374151" opacity={0.3} />
              <XAxis dataKey="name" stroke="#6B7280" />
              <YAxis stroke="#6B7280" />
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: '#1F2937', 
                  border: 'none', 
                  borderRadius: '8px',
                  color: '#F9FAFB'
                }} 
              />
              <Line type="monotone" dataKey="sms" stroke="#3B82F6" strokeWidth={2} />
              <Line type="monotone" dataKey="whatsapp" stroke="#10B981" strokeWidth={2} />
              <Line type="monotone" dataKey="email" stroke="#8B5CF6" strokeWidth={2} />
            </LineChart>
          </ResponsiveContainer>
        );
      case 'bar':
        return (
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={barData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#374151" opacity={0.3} />
              <XAxis dataKey="name" stroke="#6B7280" />
              <YAxis stroke="#6B7280" />
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: '#1F2937', 
                  border: 'none', 
                  borderRadius: '8px',
                  color: '#F9FAFB'
                }} 
              />
              <Bar dataKey="delivered" fill="#3B82F6" radius={[4, 4, 0, 0]} />
              <Bar dataKey="opened" fill="#10B981" radius={[4, 4, 0, 0]} />
              <Bar dataKey="clicked" fill="#F59E0B" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        );
      case 'pie':
        return (
          <ResponsiveContainer width="100%" height={300}>
            <PieChart>
              <Pie
                data={pieData}
                cx="50%"
                cy="50%"
                outerRadius={80}
                dataKey="value"
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
              >
                {pieData.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: '#1F2937', 
                  border: 'none', 
                  borderRadius: '8px',
                  color: '#F9FAFB'
                }} 
              />
            </PieChart>
          </ResponsiveContainer>
        );
      default:
        return null;
    }
  };

  return (
    <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
      <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">{title}</h3>
      {renderChart()}
    </div>
  );
}