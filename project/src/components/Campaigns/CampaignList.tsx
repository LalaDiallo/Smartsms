import React, { useState } from 'react';
import { 
  Plus, 
  Search, 
  Filter, 
  MoreHorizontal,
  Play,
  Pause,
  BarChart3,
  Edit,
  Trash2,
  MessageSquare,
  Mail,
  Send,
  Sparkles,
  Copy,
  Archive,
  Download,
  RefreshCw,
  TrendingUp,
  Users,
  Eye,
  MousePointer,
  Calendar,
  Clock,
  CheckCircle,
  AlertCircle,
  XCircle
} from 'lucide-react';
import { NewCampaignModal } from './NewCampaignModal';

interface Campaign {
  id: string;
  name: string;
  type: 'sms' | 'whatsapp' | 'email' | 'push';
  status: 'draft' | 'active' | 'paused' | 'completed' | 'scheduled';
  sent: number;
  delivered: number;
  opened: number;
  clicked: number;
  createdAt: string;
  scheduledAt?: string;
  budget?: number;
  revenue?: number;
}

const mockCampaigns: Campaign[] = [
  {
    id: '1',
    name: 'Promotion Été 2024',
    type: 'sms',
    status: 'active',
    sent: 12450,
    delivered: 12280,
    opened: 10956,
    clicked: 3287,
    createdAt: '2024-01-15',
    scheduledAt: '2024-01-16',
    budget: 622.50,
    revenue: 15680
  },
  {
    id: '2',
    name: 'Newsletter Mensuelle',
    type: 'email',
    status: 'completed',
    sent: 8920,
    delivered: 8756,
    opened: 6234,
    clicked: 1892,
    createdAt: '2024-01-10',
    budget: 89.20,
    revenue: 4560
  },
  {
    id: '3',
    name: 'Rappel Rendez-vous',
    type: 'whatsapp',
    status: 'scheduled',
    sent: 0,
    delivered: 0,
    opened: 0,
    clicked: 0,
    createdAt: '2024-01-20',
    scheduledAt: '2024-01-25',
    budget: 150.00,
    revenue: 0
  },
  {
    id: '4',
    name: 'Offre Flash Weekend',
    type: 'push',
    status: 'draft',
    sent: 0,
    delivered: 0,
    opened: 0,
    clicked: 0,
    createdAt: '2024-01-22',
    budget: 0,
    revenue: 0
  }
];

export function CampaignList() {
  const [campaigns, setCampaigns] = useState<Campaign[]>(mockCampaigns);
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedCampaigns, setSelectedCampaigns] = useState<string[]>([]);
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [sortBy, setSortBy] = useState<string>('created');
  const [isRefreshing, setIsRefreshing] = useState(false);

  const getStatusBadge = (status: Campaign['status']) => {
    switch (status) {
      case 'active':
        return {
          class: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
          icon: <CheckCircle className="w-3 h-3 mr-1" />,
          text: 'Active'
        };
      case 'paused':
        return {
          class: 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
          icon: <Pause className="w-3 h-3 mr-1" />,
          text: 'En pause'
        };
      case 'completed':
        return {
          class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
          icon: <CheckCircle className="w-3 h-3 mr-1" />,
          text: 'Terminée'
        };
      case 'scheduled':
        return {
          class: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
          icon: <Clock className="w-3 h-3 mr-1" />,
          text: 'Programmée'
        };
      case 'draft':
        return {
          class: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
          icon: <Edit className="w-3 h-3 mr-1" />,
          text: 'Brouillon'
        };
      default:
        return {
          class: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
          icon: <AlertCircle className="w-3 h-3 mr-1" />,
          text: status
        };
    }
  };

  const getTypeIcon = (type: Campaign['type']) => {
    switch (type) {
      case 'sms': return MessageSquare;
      case 'email': return Mail;
      case 'whatsapp': return MessageSquare;
      case 'push': return Send;
      default: return MessageSquare;
    }
  };

  const getTypeColor = (type: Campaign['type']) => {
    switch (type) {
      case 'sms': return 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/20';
      case 'email': return 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/20';
      case 'whatsapp': return 'text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/20';
      case 'push': return 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/20';
      default: return 'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/20';
    }
  };

  const handleRefresh = async () => {
    setIsRefreshing(true);
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    setIsRefreshing(false);
  };

  const handleCampaignAction = async (campaignId: string, action: string) => {
    setCampaigns(prev => prev.map(campaign => {
      if (campaign.id === campaignId) {
        switch (action) {
          case 'play':
            return { ...campaign, status: 'active' as const };
          case 'pause':
            return { ...campaign, status: 'paused' as const };
          case 'duplicate':
            // In real app, this would create a new campaign
            return campaign;
          case 'archive':
            return { ...campaign, status: 'completed' as const };
          default:
            return campaign;
        }
      }
      return campaign;
    }));
  };

  const handleBulkAction = async (action: string) => {
    if (selectedCampaigns.length === 0) return;
    
    // Simulate bulk action
    await new Promise(resolve => setTimeout(resolve, 500));
    
    if (action === 'delete') {
      setCampaigns(prev => prev.filter(c => !selectedCampaigns.includes(c.id)));
      setSelectedCampaigns([]);
    }
  };

  const filteredCampaigns = campaigns
    .filter(campaign => {
      const matchesSearch = campaign.name.toLowerCase().includes(searchTerm.toLowerCase());
      const matchesFilter = filterStatus === 'all' || campaign.status === filterStatus;
      return matchesSearch && matchesFilter;
    })
    .sort((a, b) => {
      switch (sortBy) {
        case 'name':
          return a.name.localeCompare(b.name);
        case 'status':
          return a.status.localeCompare(b.status);
        case 'performance':
          const aPerf = a.sent > 0 ? (a.opened / a.sent) : 0;
          const bPerf = b.sent > 0 ? (b.opened / b.sent) : 0;
          return bPerf - aPerf;
        default:
          return new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime();
      }
    });

  const toggleCampaignSelection = (campaignId: string) => {
    setSelectedCampaigns(prev => 
      prev.includes(campaignId) 
        ? prev.filter(id => id !== campaignId)
        : [...prev, campaignId]
    );
  };

  const totalStats = campaigns.reduce((acc, campaign) => ({
    sent: acc.sent + campaign.sent,
    delivered: acc.delivered + campaign.delivered,
    opened: acc.opened + campaign.opened,
    clicked: acc.clicked + campaign.clicked,
    revenue: acc.revenue + (campaign.revenue || 0)
  }), { sent: 0, delivered: 0, opened: 0, clicked: 0, revenue: 0 });

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Campagnes
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Gérez vos campagnes marketing multicanal
          </p>
        </div>
        <div className="flex items-center space-x-3">
          <button 
            onClick={handleRefresh}
            disabled={isRefreshing}
            className="p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 group disabled:opacity-50"
          >
            <RefreshCw className={`w-4 h-4 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300 ${isRefreshing ? 'animate-spin' : ''}`} />
          </button>
          <button 
            onClick={() => setIsModalOpen(true)}
            className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl group"
          >
            <div className="relative">
              <Plus size={20} className="group-hover:rotate-90 transition-transform duration-300" />
              <Sparkles className="absolute -top-1 -right-1 w-3 h-3 text-yellow-300 animate-pulse" />
            </div>
            <span className="font-medium">Nouvelle campagne</span>
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Messages envoyés</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">{totalStats.sent.toLocaleString()}</p>
              <p className="text-sm text-blue-600 dark:text-blue-400">+12% ce mois</p>
            </div>
            <Send className="w-8 h-8 text-blue-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taux de livraison</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {totalStats.sent > 0 ? ((totalStats.delivered / totalStats.sent) * 100).toFixed(1) : 0}%
              </p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+2.1%</p>
            </div>
            <CheckCircle className="w-8 h-8 text-emerald-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taux d'ouverture</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {totalStats.delivered > 0 ? ((totalStats.opened / totalStats.delivered) * 100).toFixed(1) : 0}%
              </p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+5.3%</p>
            </div>
            <Eye className="w-8 h-8 text-purple-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taux de clic</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {totalStats.opened > 0 ? ((totalStats.clicked / totalStats.opened) * 100).toFixed(1) : 0}%
              </p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+3.7%</p>
            </div>
            <MousePointer className="w-8 h-8 text-amber-600" />
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Revenus générés</p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">€{totalStats.revenue.toLocaleString()}</p>
              <p className="text-sm text-emerald-600 dark:text-emerald-400">+18.5%</p>
            </div>
            <TrendingUp className="w-8 h-8 text-emerald-600" />
          </div>
        </div>
      </div>

      {/* Filters and Actions */}
      <div className="flex flex-col sm:flex-row gap-4">
        <div className="flex-1 relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            type="text"
            placeholder="Rechercher une campagne..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white transition-all"
          />
        </div>
        <div className="flex items-center space-x-3">
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
          >
            <option value="all">Tous les statuts</option>
            <option value="active">Actives</option>
            <option value="paused">En pause</option>
            <option value="completed">Terminées</option>
            <option value="scheduled">Programmées</option>
            <option value="draft">Brouillons</option>
          </select>
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
          >
            <option value="created">Date de création</option>
            <option value="name">Nom</option>
            <option value="status">Statut</option>
            <option value="performance">Performance</option>
          </select>
          {selectedCampaigns.length > 0 && (
            <div className="flex items-center space-x-2">
              <button 
                onClick={() => handleBulkAction('archive')}
                className="flex items-center space-x-2 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors"
              >
                <Archive size={16} />
                <span>Archiver ({selectedCampaigns.length})</span>
              </button>
              <button 
                onClick={() => handleBulkAction('delete')}
                className="flex items-center space-x-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
              >
                <Trash2 size={16} />
                <span>Supprimer</span>
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Campaign Cards */}
      <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        {filteredCampaigns.map((campaign) => {
          const TypeIcon = getTypeIcon(campaign.type);
          const statusInfo = getStatusBadge(campaign.status);
          const deliveryRate = campaign.sent > 0 ? (campaign.delivered / campaign.sent * 100).toFixed(1) : '0';
          const openRate = campaign.delivered > 0 ? (campaign.opened / campaign.delivered * 100).toFixed(1) : '0';
          const clickRate = campaign.opened > 0 ? (campaign.clicked / campaign.opened * 100).toFixed(1) : '0';
          const roi = campaign.budget && campaign.budget > 0 && campaign.revenue ? ((campaign.revenue - campaign.budget) / campaign.budget * 100).toFixed(1) : '0';

          return (
            <div
              key={campaign.id}
              className={`bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border-2 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] ${
                selectedCampaigns.includes(campaign.id)
                  ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                  : 'border-gray-200 dark:border-gray-700'
              }`}
            >
              {/* Header */}
              <div className="flex items-start justify-between mb-4">
                <div className="flex items-center space-x-3">
                  <input
                    type="checkbox"
                    checked={selectedCampaigns.includes(campaign.id)}
                    onChange={() => toggleCampaignSelection(campaign.id)}
                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <div className={`p-2 rounded-lg ${getTypeColor(campaign.type)}`}>
                    <TypeIcon className="w-5 h-5" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900 dark:text-white">
                      {campaign.name}
                    </h3>
                    <span className={`inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${statusInfo.class}`}>
                      {statusInfo.icon}
                      {statusInfo.text}
                    </span>
                  </div>
                </div>
                <div className="relative">
                  <button className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group">
                    <MoreHorizontal className="w-4 h-4 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
                  </button>
                </div>
              </div>

              {/* Stats Grid */}
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className="text-lg font-bold text-gray-900 dark:text-white">
                    {campaign.sent.toLocaleString()}
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400">Envoyés</p>
                  <p className="text-xs text-emerald-600 dark:text-emerald-400">
                    {deliveryRate}% livrés
                  </p>
                </div>
                <div className="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className="text-lg font-bold text-gray-900 dark:text-white">
                    {campaign.opened.toLocaleString()}
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400">Ouverts</p>
                  <p className="text-xs text-blue-600 dark:text-blue-400">
                    {openRate}% taux d'ouverture
                  </p>
                </div>
                <div className="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className="text-lg font-bold text-gray-900 dark:text-white">
                    {campaign.clicked.toLocaleString()}
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400">Clics</p>
                  <p className="text-xs text-purple-600 dark:text-purple-400">
                    {clickRate}% taux de clic
                  </p>
                </div>
                <div className="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className="text-lg font-bold text-gray-900 dark:text-white">
                    €{(campaign.revenue || 0).toLocaleString()}
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400">Revenus</p>
                  <p className={`text-xs ${parseFloat(roi) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'}`}>
                    {roi}% ROI
                  </p>
                </div>
              </div>

              {/* Actions */}
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                  {campaign.status === 'active' ? (
                    <button 
                      onClick={() => handleCampaignAction(campaign.id, 'pause')}
                      className="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all duration-300 group"
                      title="Mettre en pause"
                    >
                      <Pause size={16} className="group-hover:scale-110 transition-transform" />
                    </button>
                  ) : campaign.status === 'paused' ? (
                    <button 
                      onClick={() => handleCampaignAction(campaign.id, 'play')}
                      className="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all duration-300 group"
                      title="Reprendre"
                    >
                      <Play size={16} className="group-hover:scale-110 transition-transform" />
                    </button>
                  ) : campaign.status === 'draft' ? (
                    <button 
                      onClick={() => handleCampaignAction(campaign.id, 'play')}
                      className="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-300 group"
                      title="Lancer"
                    >
                      <Play size={16} className="group-hover:scale-110 transition-transform" />
                    </button>
                  ) : null}
                  
                  <button className="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-300 group" title="Statistiques">
                    <BarChart3 size={16} className="group-hover:scale-110 transition-transform" />
                  </button>
                  
                  <button className="p-2 text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all duration-300 group" title="Modifier">
                    <Edit size={16} className="group-hover:scale-110 transition-transform" />
                  </button>
                  
                  <button 
                    onClick={() => handleCampaignAction(campaign.id, 'duplicate')}
                    className="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-all duration-300 group" 
                    title="Dupliquer"
                  >
                    <Copy size={16} className="group-hover:scale-110 transition-transform" />
                  </button>
                  
                  <button className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-300 group" title="Supprimer">
                    <Trash2 size={16} className="group-hover:scale-110 transition-transform" />
                  </button>
                </div>
                
                <div className="text-right">
                  <p className="text-xs text-gray-500 dark:text-gray-400">
                    Créée le {new Date(campaign.createdAt).toLocaleDateString('fr-FR')}
                  </p>
                  {campaign.scheduledAt && campaign.status === 'scheduled' && (
                    <p className="text-xs text-purple-600 dark:text-purple-400">
                      Programmée le {new Date(campaign.scheduledAt).toLocaleDateString('fr-FR')}
                    </p>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Empty State */}
      {filteredCampaigns.length === 0 && (
        <div className="text-center py-12">
          <div className="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <MessageSquare className="w-12 h-12 text-gray-400" />
          </div>
          <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {searchTerm || filterStatus !== 'all' ? 'Aucune campagne trouvée' : 'Aucune campagne'}
          </h3>
          <p className="text-gray-500 dark:text-gray-400 mb-6">
            {searchTerm || filterStatus !== 'all' 
              ? 'Essayez de modifier vos filtres de recherche'
              : 'Commencez par créer votre première campagne'
            }
          </p>
          {(!searchTerm && filterStatus === 'all') && (
            <button 
              onClick={() => setIsModalOpen(true)}
              className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl"
            >
              Créer une campagne
            </button>
          )}
        </div>
      )}

      {/* New Campaign Modal */}
      <NewCampaignModal 
        isOpen={isModalOpen} 
        onClose={() => setIsModalOpen(false)} 
      />
    </div>
  );
}