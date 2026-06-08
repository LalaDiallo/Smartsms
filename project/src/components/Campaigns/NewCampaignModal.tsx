import React, { useState } from 'react';
import { 
  X, 
  MessageSquare, 
  Mail, 
  Send, 
  Smartphone, 
  Users, 
  Calendar, 
  Target, 
  Sparkles, 
  ArrowRight, 
  Loader2,
  ArrowLeft,
  CheckCircle,
  Clock,
  Globe,
  Image,
  Type,
  Link,
  Zap,
  Settings,
  Eye,
  Play
} from 'lucide-react';

interface NewCampaignModalProps {
  isOpen: boolean;
  onClose: () => void;
}

interface CampaignData {
  name: string;
  type: string;
  audience: string;
  schedule: 'now' | 'later';
  scheduledDate: string;
  scheduledTime: string;
  message: {
    subject: string;
    content: string;
    media?: string;
    cta?: string;
    ctaUrl?: string;
  };
  settings: {
    trackOpens: boolean;
    trackClicks: boolean;
    allowUnsubscribe: boolean;
    timezone: string;
  };
}

export function NewCampaignModal({ isOpen, onClose }: NewCampaignModalProps) {
  const [step, setStep] = useState(1);
  const [isCreating, setIsCreating] = useState(false);
  const [campaignData, setCampaignData] = useState<CampaignData>({
    name: '',
    type: '',
    audience: '',
    schedule: 'now',
    scheduledDate: '',
    scheduledTime: '',
    message: {
      subject: '',
      content: '',
      media: '',
      cta: '',
      ctaUrl: ''
    },
    settings: {
      trackOpens: true,
      trackClicks: true,
      allowUnsubscribe: true,
      timezone: 'Europe/Paris'
    }
  });

  if (!isOpen) return null;

  const campaignTypes = [
    {
      id: 'sms',
      name: 'SMS',
      description: 'Messages texte directs et efficaces',
      icon: MessageSquare,
      color: 'bg-blue-500',
      popular: true,
      features: ['160 caractères', 'Livraison instantanée', 'Taux d\'ouverture 98%']
    },
    {
      id: 'whatsapp',
      name: 'WhatsApp',
      description: 'Messages riches avec médias',
      icon: Smartphone,
      color: 'bg-green-500',
      popular: true,
      features: ['Messages multimédias', 'Boutons interactifs', 'Conversations']
    },
    {
      id: 'email',
      name: 'Email',
      description: 'Campagnes email personnalisées',
      icon: Mail,
      color: 'bg-purple-500',
      popular: false,
      features: ['Design HTML', 'Personnalisation', 'Analytics détaillées']
    },
    {
      id: 'push',
      name: 'Push',
      description: 'Notifications push mobiles',
      icon: Send,
      color: 'bg-orange-500',
      popular: false,
      features: ['Notifications instantanées', 'Rich media', 'Géolocalisation']
    }
  ];

  const audiences = [
    { 
      id: 'all', 
      name: 'Tous les contacts', 
      count: '2,847', 
      description: 'Votre base complète',
      engagement: 78,
      lastCampaign: 'Il y a 3 jours'
    },
    { 
      id: 'vip', 
      name: 'Clients VIP', 
      count: '234', 
      description: 'Clients premium et fidèles',
      engagement: 92,
      lastCampaign: 'Il y a 1 semaine'
    },
    { 
      id: 'new', 
      name: 'Nouveaux clients', 
      count: '456', 
      description: 'Inscrits récemment',
      engagement: 85,
      lastCampaign: 'Jamais'
    },
    { 
      id: 'inactive', 
      name: 'Clients inactifs', 
      count: '890', 
      description: 'Pas d\'activité récente',
      engagement: 45,
      lastCampaign: 'Il y a 2 mois'
    }
  ];

  const handleCreate = async () => {
    setIsCreating(true);
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 3000));
      onClose();
      // Reset form
      setStep(1);
      setCampaignData({
        name: '',
        type: '',
        audience: '',
        schedule: 'now',
        scheduledDate: '',
        scheduledTime: '',
        message: {
          subject: '',
          content: '',
          media: '',
          cta: '',
          ctaUrl: ''
        },
        settings: {
          trackOpens: true,
          trackClicks: true,
          allowUnsubscribe: true,
          timezone: 'Europe/Paris'
        }
      });
    } catch (error) {
      console.error('Campaign creation failed:', error);
    } finally {
      setIsCreating(false);
    }
  };

  const canProceed = () => {
    switch (step) {
      case 1: return campaignData.type;
      case 2: return campaignData.audience;
      case 3: return campaignData.name && campaignData.message.content;
      case 4: return campaignData.schedule === 'now' || (campaignData.scheduledDate && campaignData.scheduledTime);
      default: return false;
    }
  };

  const getStepTitle = () => {
    switch (step) {
      case 1: return 'Type de campagne';
      case 2: return 'Audience cible';
      case 3: return 'Contenu du message';
      case 4: return 'Planification';
      case 5: return 'Paramètres avancés';
      case 6: return 'Révision finale';
      default: return 'Nouvelle campagne';
    }
  };

  const selectedType = campaignTypes.find(t => t.id === campaignData.type);
  const selectedAudience = audiences.find(a => a.id === campaignData.audience);

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
              <Sparkles className="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                {getStepTitle()}
              </h2>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Étape {step} sur 6
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group"
          >
            <X className="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
          </button>
        </div>

        {/* Progress Bar */}
        <div className="px-6 py-4">
          <div className="flex items-center space-x-2">
            {[1, 2, 3, 4, 5, 6].map((stepNumber) => (
              <div key={stepNumber} className="flex items-center flex-1">
                <div className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300 ${
                  step >= stepNumber 
                    ? 'bg-blue-600 text-white scale-110' 
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-500 scale-100'
                }`}>
                  {step > stepNumber ? (
                    <CheckCircle className="w-4 h-4" />
                  ) : (
                    stepNumber
                  )}
                </div>
                {stepNumber < 6 && (
                  <div className={`flex-1 h-2 mx-2 rounded transition-all duration-500 ${
                    step > stepNumber ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                  }`} />
                )}
              </div>
            ))}
          </div>
        </div>

        {/* Content */}
        <div className="p-6 overflow-y-auto max-h-[60vh]">
          {/* Step 1: Campaign Type */}
          {step === 1 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Choisissez votre canal de communication
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Sélectionnez le type de campagne qui correspond le mieux à vos objectifs
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {campaignTypes.map((type) => {
                  const Icon = type.icon;
                  const isSelected = campaignData.type === type.id;
                  return (
                    <button
                      key={type.id}
                      onClick={() => setCampaignData(prev => ({ ...prev, type: type.id }))}
                      className={`relative p-6 rounded-xl border-2 transition-all duration-300 text-left group hover:scale-105 hover:shadow-lg ${
                        isSelected
                          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-lg scale-105'
                          : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                      }`}
                    >
                      {type.popular && (
                        <div className="absolute -top-2 -right-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                          Populaire
                        </div>
                      )}
                      <div className={`w-12 h-12 ${type.color} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300`}>
                        <Icon className="w-6 h-6 text-white" />
                      </div>
                      <h4 className="font-semibold text-gray-900 dark:text-white mb-2">
                        {type.name}
                      </h4>
                      <p className="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {type.description}
                      </p>
                      <div className="space-y-1">
                        {type.features.map((feature, index) => (
                          <div key={index} className="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <CheckCircle className="w-3 h-3 mr-2 text-green-500" />
                            {feature}
                          </div>
                        ))}
                      </div>
                      {isSelected && (
                        <div className="absolute top-4 right-4">
                          <CheckCircle className="w-6 h-6 text-blue-600 animate-bounce" />
                        </div>
                      )}
                    </button>
                  );
                })}
              </div>
            </div>
          )}

          {/* Step 2: Audience Selection */}
          {step === 2 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Sélectionnez votre audience
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Choisissez le segment de contacts à cibler pour votre campagne {selectedType?.name}
                </p>
              </div>

              <div className="space-y-4">
                {audiences.map((audience) => {
                  const isSelected = campaignData.audience === audience.id;
                  return (
                    <button
                      key={audience.id}
                      onClick={() => setCampaignData(prev => ({ ...prev, audience: audience.id }))}
                      className={`w-full p-4 rounded-xl border-2 transition-all duration-300 text-left hover:scale-[1.02] hover:shadow-md ${
                        isSelected
                          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-md'
                          : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                      }`}
                    >
                      <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-4">
                          <div className="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <Users className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                          </div>
                          <div>
                            <h4 className="font-semibold text-gray-900 dark:text-white">
                              {audience.name}
                            </h4>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                              {audience.description}
                            </p>
                            <p className="text-xs text-gray-500 dark:text-gray-400">
                              Dernière campagne: {audience.lastCampaign}
                            </p>
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="text-2xl font-bold text-gray-900 dark:text-white">
                            {audience.count}
                          </div>
                          <div className="text-xs text-gray-500 dark:text-gray-400 mb-1">
                            contacts
                          </div>
                          <div className="flex items-center space-x-1">
                            <div className="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                              <div 
                                className="bg-green-500 h-1 rounded-full transition-all duration-500" 
                                style={{ width: `${audience.engagement}%` }}
                              />
                            </div>
                            <span className="text-xs text-green-600 dark:text-green-400">
                              {audience.engagement}%
                            </span>
                          </div>
                        </div>
                      </div>
                      {isSelected && (
                        <div className="absolute top-4 right-4">
                          <CheckCircle className="w-6 h-6 text-blue-600 animate-pulse" />
                        </div>
                      )}
                    </button>
                  );
                })}
              </div>
            </div>
          )}

          {/* Step 3: Message Content */}
          {step === 3 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Créez votre message
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Rédigez le contenu de votre campagne {selectedType?.name}
                </p>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Message Form */}
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Nom de la campagne *
                    </label>
                    <input
                      type="text"
                      value={campaignData.name}
                      onChange={(e) => setCampaignData(prev => ({ ...prev, name: e.target.value }))}
                      className="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white transition-all"
                      placeholder="Ex: Promotion Été 2024"
                    />
                  </div>

                  {(campaignData.type === 'email' || campaignData.type === 'push') && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sujet *
                      </label>
                      <input
                        type="text"
                        value={campaignData.message.subject}
                        onChange={(e) => setCampaignData(prev => ({ 
                          ...prev, 
                          message: { ...prev.message, subject: e.target.value }
                        }))}
                        className="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white transition-all"
                        placeholder="Objet de votre message"
                      />
                    </div>
                  )}

                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Message *
                    </label>
                    <textarea
                      rows={6}
                      value={campaignData.message.content}
                      onChange={(e) => setCampaignData(prev => ({ 
                        ...prev, 
                        message: { ...prev.message, content: e.target.value }
                      }))}
                      className="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white transition-all resize-none"
                      placeholder={
                        campaignData.type === 'sms' 
                          ? "Votre message SMS (160 caractères max)" 
                          : "Rédigez votre message..."
                      }
                    />
                    {campaignData.type === 'sms' && (
                      <div className="flex justify-between text-xs mt-1">
                        <span className="text-gray-500 dark:text-gray-400">
                          Caractères utilisés
                        </span>
                        <span className={`font-medium ${
                          campaignData.message.content.length > 160 
                            ? 'text-red-600' 
                            : 'text-green-600'
                        }`}>
                          {campaignData.message.content.length}/160
                        </span>
                      </div>
                    )}
                  </div>

                  {campaignData.type === 'whatsapp' && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Média (optionnel)
                      </label>
                      <div className="flex items-center space-x-2">
                        <button className="flex-1 p-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 transition-colors group">
                          <Image className="w-6 h-6 mx-auto text-gray-400 group-hover:text-blue-500 mb-2" />
                          <span className="text-sm text-gray-500 group-hover:text-blue-500">
                            Ajouter une image
                          </span>
                        </button>
                      </div>
                    </div>
                  )}

                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Bouton d'action (optionnel)
                    </label>
                    <div className="grid grid-cols-2 gap-3">
                      <input
                        type="text"
                        value={campaignData.message.cta}
                        onChange={(e) => setCampaignData(prev => ({ 
                          ...prev, 
                          message: { ...prev.message, cta: e.target.value }
                        }))}
                        className="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                        placeholder="Texte du bouton"
                      />
                      <input
                        type="url"
                        value={campaignData.message.ctaUrl}
                        onChange={(e) => setCampaignData(prev => ({ 
                          ...prev, 
                          message: { ...prev.message, ctaUrl: e.target.value }
                        }))}
                        className="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                        placeholder="https://..."
                      />
                    </div>
                  </div>
                </div>

                {/* Preview */}
                <div className="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                  <div className="flex items-center space-x-2 mb-4">
                    <Eye className="w-4 h-4 text-gray-500" />
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                      Aperçu
                    </span>
                  </div>
                  
                  <div className="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    {campaignData.type === 'sms' && (
                      <div className="space-y-2">
                        <div className="text-xs text-gray-500 dark:text-gray-400">SMS</div>
                        <div className="text-sm text-gray-900 dark:text-white">
                          {campaignData.message.content || "Votre message apparaîtra ici..."}
                        </div>
                      </div>
                    )}
                    
                    {campaignData.type === 'email' && (
                      <div className="space-y-3">
                        <div className="text-xs text-gray-500 dark:text-gray-400">Email</div>
                        <div className="font-medium text-gray-900 dark:text-white">
                          {campaignData.message.subject || "Objet du message"}
                        </div>
                        <div className="text-sm text-gray-700 dark:text-gray-300">
                          {campaignData.message.content || "Contenu de votre email..."}
                        </div>
                      </div>
                    )}
                    
                    {campaignData.type === 'whatsapp' && (
                      <div className="space-y-2">
                        <div className="text-xs text-gray-500 dark:text-gray-400">WhatsApp</div>
                        <div className="bg-green-100 dark:bg-green-900/20 p-3 rounded-lg">
                          <div className="text-sm text-gray-900 dark:text-white">
                            {campaignData.message.content || "Votre message WhatsApp..."}
                          </div>
                          {campaignData.message.cta && (
                            <button className="mt-2 bg-green-600 text-white px-3 py-1 rounded text-xs">
                              {campaignData.message.cta}
                            </button>
                          )}
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Step 4: Scheduling */}
          {step === 4 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Planifiez votre campagne
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Choisissez quand envoyer votre campagne
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <button
                  onClick={() => setCampaignData(prev => ({ ...prev, schedule: 'now' }))}
                  className={`p-6 rounded-xl border-2 transition-all duration-300 text-left hover:scale-105 ${
                    campaignData.schedule === 'now'
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                  }`}
                >
                  <div className="flex items-center space-x-3 mb-3">
                    <div className="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                      <Zap className="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <h4 className="font-semibold text-gray-900 dark:text-white">
                      Envoyer maintenant
                    </h4>
                  </div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">
                    La campagne sera envoyée immédiatement après création
                  </p>
                </button>

                <button
                  onClick={() => setCampaignData(prev => ({ ...prev, schedule: 'later' }))}
                  className={`p-6 rounded-xl border-2 transition-all duration-300 text-left hover:scale-105 ${
                    campaignData.schedule === 'later'
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                  }`}
                >
                  <div className="flex items-center space-x-3 mb-3">
                    <div className="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                      <Calendar className="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h4 className="font-semibold text-gray-900 dark:text-white">
                      Programmer
                    </h4>
                  </div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">
                    Planifiez l'envoi pour une date et heure spécifiques
                  </p>
                </button>
              </div>

              {campaignData.schedule === 'later' && (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Date
                    </label>
                    <input
                      type="date"
                      value={campaignData.scheduledDate}
                      onChange={(e) => setCampaignData(prev => ({ ...prev, scheduledDate: e.target.value }))}
                      className="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Heure
                    </label>
                    <input
                      type="time"
                      value={campaignData.scheduledTime}
                      onChange={(e) => setCampaignData(prev => ({ ...prev, scheduledTime: e.target.value }))}
                      className="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    />
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Step 5: Advanced Settings */}
          {step === 5 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Paramètres avancés
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Configurez les options de suivi et de conformité
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">
                    Suivi et analytics
                  </h4>
                  
                  <div className="space-y-3">
                    <label className="flex items-center justify-between">
                      <span className="text-sm text-gray-700 dark:text-gray-300">
                        Suivi des ouvertures
                      </span>
                      <button
                        onClick={() => setCampaignData(prev => ({ 
                          ...prev, 
                          settings: { ...prev.settings, trackOpens: !prev.settings.trackOpens }
                        }))}
                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                          campaignData.settings.trackOpens ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                        }`}
                      >
                        <span
                          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                            campaignData.settings.trackOpens ? 'translate-x-6' : 'translate-x-1'
                          }`}
                        />
                      </button>
                    </label>

                    <label className="flex items-center justify-between">
                      <span className="text-sm text-gray-700 dark:text-gray-300">
                        Suivi des clics
                      </span>
                      <button
                        onClick={() => setCampaignData(prev => ({ 
                          ...prev, 
                          settings: { ...prev.settings, trackClicks: !prev.settings.trackClicks }
                        }))}
                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                          campaignData.settings.trackClicks ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                        }`}
                      >
                        <span
                          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                            campaignData.settings.trackClicks ? 'translate-x-6' : 'translate-x-1'
                          }`}
                        />
                      </button>
                    </label>

                    <label className="flex items-center justify-between">
                      <span className="text-sm text-gray-700 dark:text-gray-300">
                        Lien de désabonnement
                      </span>
                      <button
                        onClick={() => setCampaignData(prev => ({ 
                          ...prev, 
                          settings: { ...prev.settings, allowUnsubscribe: !prev.settings.allowUnsubscribe }
                        }))}
                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                          campaignData.settings.allowUnsubscribe ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                        }`}
                      >
                        <span
                          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                            campaignData.settings.allowUnsubscribe ? 'translate-x-6' : 'translate-x-1'
                          }`}
                        />
                      </button>
                    </label>
                  </div>
                </div>

                <div className="space-y-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">
                    Localisation
                  </h4>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Fuseau horaire
                    </label>
                    <select
                      value={campaignData.settings.timezone}
                      onChange={(e) => setCampaignData(prev => ({ 
                        ...prev, 
                        settings: { ...prev.settings, timezone: e.target.value }
                      }))}
                      className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    >
                      <option value="Europe/Paris">Europe/Paris (UTC+1)</option>
                      <option value="Europe/London">Europe/London (UTC+0)</option>
                      <option value="America/New_York">America/New_York (UTC-5)</option>
                      <option value="Asia/Tokyo">Asia/Tokyo (UTC+9)</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Step 6: Final Review */}
          {step === 6 && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Révision finale
                </h3>
                <p className="text-gray-600 dark:text-gray-400">
                  Vérifiez tous les détails avant de lancer votre campagne
                </p>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Campaign Summary */}
                <div className="space-y-4">
                  <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                      Détails de la campagne
                    </h4>
                    <div className="space-y-2 text-sm">
                      <div className="flex justify-between">
                        <span className="text-gray-600 dark:text-gray-400">Nom:</span>
                        <span className="text-gray-900 dark:text-white font-medium">
                          {campaignData.name}
                        </span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-600 dark:text-gray-400">Type:</span>
                        <span className="text-gray-900 dark:text-white font-medium">
                          {selectedType?.name}
                        </span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-600 dark:text-gray-400">Audience:</span>
                        <span className="text-gray-900 dark:text-white font-medium">
                          {selectedAudience?.name} ({selectedAudience?.count})
                        </span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-600 dark:text-gray-400">Planification:</span>
                        <span className="text-gray-900 dark:text-white font-medium">
                          {campaignData.schedule === 'now' 
                            ? 'Immédiat' 
                            : `${campaignData.scheduledDate} à ${campaignData.scheduledTime}`
                          }
                        </span>
                      </div>
                    </div>
                  </div>

                  <div className="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                    <h4 className="font-medium text-blue-900 dark:text-blue-100 mb-2">
                      Estimation des coûts
                    </h4>
                    <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">
                      €{(parseInt(selectedAudience?.count.replace(',', '') || '0') * 0.05).toFixed(2)}
                    </div>
                    <p className="text-sm text-blue-700 dark:text-blue-300">
                      Basé sur {selectedAudience?.count} contacts
                    </p>
                  </div>
                </div>

                {/* Message Preview */}
                <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                  <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                    Aperçu du message
                  </h4>
                  <div className="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    {campaignData.message.subject && (
                      <div className="font-medium text-gray-900 dark:text-white mb-2">
                        {campaignData.message.subject}
                      </div>
                    )}
                    <div className="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                      {campaignData.message.content}
                    </div>
                    {campaignData.message.cta && (
                      <button className="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        {campaignData.message.cta}
                      </button>
                    )}
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700">
          <button
            onClick={() => step > 1 ? setStep(step - 1) : onClose()}
            className="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors flex items-center space-x-2 group"
          >
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
            <span>{step > 1 ? 'Précédent' : 'Annuler'}</span>
          </button>

          <div className="flex space-x-3">
            {step < 6 ? (
              <button
                onClick={() => setStep(step + 1)}
                disabled={!canProceed()}
                className="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 group hover:scale-105 active:scale-95"
              >
                <span>Suivant</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>
            ) : (
              <button
                onClick={handleCreate}
                disabled={!canProceed() || isCreating}
                className="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 group hover:scale-105 active:scale-95"
              >
                {isCreating ? (
                  <>
                    <Loader2 className="w-4 h-4 animate-spin" />
                    <span>Création en cours...</span>
                  </>
                ) : (
                  <>
                    <Play className="w-4 h-4" />
                    <span>Lancer la campagne</span>
                  </>
                )}
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}