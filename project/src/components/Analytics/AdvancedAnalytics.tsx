import React, { useState } from 'react';
import { 
  BarChart3, 
  TrendingUp, 
  Users, 
  MessageSquare,
  Eye,
  MousePointer,
  Calendar,
  Filter,
  Download,
  RefreshCw,
  Target,
  DollarSign
} from 'lucide-react';
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
  Cell,
  AreaChart,
  Area
} from 'recharts';

const performanceData = [
  { date: '01/01', sent: 4000, delivered: 3800, opened: 2800, clicked: 1200, revenue: 2400 },
  { date: '02/01', sent: 3000, delivered: 2900, opened: 2200, clicked: 980, revenue: 1800 },
  { date: '03/01', sent: 5000, delivered: 4800, opened: 3600, clicked: 1800, revenue: 3200 },
  { date: '04/01', sent: 2780, delivered: 2650, opened: 2100, clicked: 1050, revenue: 2100 },
  { date: '05/01', sent: 1890, delivered: 1800, opened: 1400, clicked: 700, revenue: 1400 },
  { date: '06/01', sent: 2390, delivered: 2280, opened: 1800, clicked: 900, revenue: 1800 },
  { date: '07/01', sent: 3490, delivered: 3300, opened: 2500, clicked: 1250, revenue: 2500 }
];

const channelData = [
  { name: 'SMS', value: 45, color: '#3B82F6', revenue: 18500 },
  { name: 'WhatsApp', value: 30, color: '#10B981', revenue: 12300 },
  { name: 'Email', value: 20, color: '#8B5CF6', revenue: 8200 },
  { name: 'Push', value: 5, color: '#F59E0B', revenue: 2100 }
];

const audienceData = [
  { segment: '18-25 ans', users: 1250, engagement: 78, revenue: 15600 },
  { segment: '26-35 ans', users: 2340, engagement: 85, revenue: 28900 },
  { segment: '36-45 ans', users: 1890, engagement: 72, revenue: 22400 },
  { segment: '46-55 ans', users: 980, engagement: 68, revenue: 18200 },
  { segment: '55+ ans', users: 560, engagement: 65, revenue: 12800 }
];

export function AdvancedAnalytics() {
  const [dateRange, setDateRange] = useState('7d');
  const [selectedMetric, setSelectedMetric] = useState('all');

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Analyses avancées
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Insights détaillés sur vos performances marketing
          </p>
        </div>
        <div className="flex items-center space-x-3">
          <select
            value={dateRange}
            onChange={(e) => setDateRange(e.target.value)}
            className="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
          >
            <option value="7d">7 derniers jours</option>
            <option value="30d">30 derniers jours</option>
            <option value="90d">90 derniers jours</option>
            <option value="1y">1 an</option>
          </select>
          <button className="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <Filter size={16} />
            <span className="text-gray-700 dark:text-gray-300">Filtres</span>
          </button>
          <button className="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            <Download size={16} />
            <span>Exporter</span>
          </button>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">ROI Moyen</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">324%</p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+12.5% vs période précédente</p>
            </div>
            <TrendingUp className="w-8 h-8 text-emerald-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Coût par acquisition</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">€12.45</p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">-8.2% vs période précédente</p>
            </div>
            <Target className="w-8 h-8 text-blue-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Valeur vie client</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">€456.78</p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+15.3% vs période précédente</p>
            </div>
            <DollarSign className="w-8 h-8 text-amber-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taux de conversion</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">18.7%</p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+3.1% vs période précédente</p>
            </div>
            <MousePointer className="w-8 h-8 text-purple-600" />
          </div>
        </div>
      </div>

      {/* Performance Chart */}
      <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            Performance des campagnes
          </h3>
          <div className="flex items-center space-x-2">
            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
              <RefreshCw className="w-4 h-4 text-gray-500" />
            </button>
          </div>
        </div>
        <ResponsiveContainer width="100%" height={400}>
          <AreaChart data={performanceData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#374151" opacity={0.3} />
            <XAxis dataKey="date" stroke="#6B7280" />
            <YAxis stroke="#6B7280" />
            <Tooltip 
              contentStyle={{ 
                backgroundColor: '#1F2937', 
                border: 'none', 
                borderRadius: '8px',
                color: '#F9FAFB'
              }} 
            />
            <Area type="monotone" dataKey="sent" stackId="1" stroke="#3B82F6" fill="#3B82F6" fillOpacity={0.1} />
            <Area type="monotone" dataKey="delivered" stackId="2" stroke="#10B981" fill="#10B981" fillOpacity={0.2} />
            <Area type="monotone" dataKey="opened" stackId="3" stroke="#8B5CF6" fill="#8B5CF6" fillOpacity={0.3} />
            <Area type="monotone" dataKey="clicked" stackId="4" stroke="#F59E0B" fill="#F59E0B" fillOpacity={0.4} />
          </AreaChart>
        </ResponsiveContainer>
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Channel Performance */}
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Performance par canal
          </h3>
          <div className="flex items-center justify-center mb-4">
            <ResponsiveContainer width="100%" height={250}>
              <PieChart>
                <Pie
                  data={channelData}
                  cx="50%"
                  cy="50%"
                  outerRadius={80}
                  dataKey="value"
                  label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                >
                  {channelData.map((entry, index) => (
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
          </div>
          <div className="space-y-2">
            {channelData.map((channel, index) => (
              <div key={index} className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                  <div className={`w-3 h-3 rounded-full`} style={{ backgroundColor: channel.color }} />
                  <span className="text-sm text-gray-700 dark:text-gray-300">{channel.name}</span>
                </div>
                <span className="text-sm font-medium text-gray-900 dark:text-white">
                  €{channel.revenue.toLocaleString()}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Revenue Trend */}
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Évolution du chiffre d'affaires
          </h3>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={performanceData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#374151" opacity={0.3} />
              <XAxis dataKey="date" stroke="#6B7280" />
              <YAxis stroke="#6B7280" />
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: '#1F2937', 
                  border: 'none', 
                  borderRadius: '8px',
                  color: '#F9FAFB'
                }} 
              />
              <Line 
                type="monotone" 
                dataKey="revenue" 
                stroke="#10B981" 
                strokeWidth={3}
                dot={{ fill: '#10B981', strokeWidth: 2, r: 4 }}
              />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Audience Analysis */}
      <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Analyse de l'audience
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-200 dark:border-gray-700">
                <th className="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Segment</th>
                <th className="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Utilisateurs</th>
                <th className="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Engagement</th>
                <th className="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Revenus</th>
                <th className="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Tendance</th>
              </tr>
            </thead>
            <tbody>
              {audienceData.map((segment, index) => (
                <tr key={index} className="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td className="py-3 px-4 text-gray-900 dark:text-white font-medium">
                    {segment.segment}
                  </td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-400">
                    {segment.users.toLocaleString()}
                  </td>
                  <td className="py-3 px-4">
                    <div className="flex items-center space-x-2">
                      <div className="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div 
                          className="bg-blue-600 h-2 rounded-full" 
                          style={{ width: `${segment.engagement}%` }}
                        />
                      </div>
                      <span className="text-sm text-gray-600 dark:text-gray-400">
                        {segment.engagement}%
                      </span>
                    </div>
                  </td>
                  <td className="py-3 px-4 text-gray-900 dark:text-white font-medium">
                    €{segment.revenue.toLocaleString()}
                  </td>
                  <td className="py-3 px-4">
                    <TrendingUp className="w-4 h-4 text-emerald-600" />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}