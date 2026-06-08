import React, { useState } from 'react';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { AuthContainer } from './components/Auth/AuthContainer';
import { Sidebar } from './components/Layout/Sidebar';
import { Header } from './components/Layout/Header';
import { Dashboard } from './components/Dashboard/Dashboard';
import { CampaignList } from './components/Campaigns/CampaignList';
import { ContactList } from './components/Contacts/ContactList';
import { LoyaltyProgram } from './components/Loyalty/LoyaltyProgram';
import { AdvancedAnalytics } from './components/Analytics/AdvancedAnalytics';
import { AdvancedTargeting } from './components/Targeting/AdvancedTargeting';
import { SecurityCompliance } from './components/Security/SecurityCompliance';
import { PlatformSettings } from './components/Settings/PlatformSettings';

function AppContent() {
  const { isAuthenticated } = useAuth();
  const [currentPage, setCurrentPage] = useState('dashboard');
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  if (!isAuthenticated) {
    return <AuthContainer />;
  }

  const renderPage = () => {
    switch (currentPage) {
      case 'dashboard':
        return <Dashboard />;
      case 'campaigns':
        return <CampaignList />;
      case 'contacts':
        return <ContactList />;
      case 'loyalty':
        return <LoyaltyProgram />;
      case 'analytics':
        return <AdvancedAnalytics />;
      case 'targeting':
        return <AdvancedTargeting />;
      case 'security':
        return <SecurityCompliance />;
      case 'settings':
        return <PlatformSettings />;
      default:
        return <Dashboard />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex">
      <Sidebar
        currentPage={currentPage}
        onPageChange={setCurrentPage}
        isCollapsed={sidebarCollapsed}
        onToggleCollapse={() => setSidebarCollapsed(!sidebarCollapsed)}
      />
      <div className="flex-1 flex flex-col overflow-hidden">
        <Header />
        <main className="flex-1 overflow-y-auto">
          {renderPage()}
        </main>
      </div>
    </div>
  );
}

function App() {
  return (
    <AuthProvider>
      <div className="min-h-screen">
        <AppContent />
      </div>
    </AuthProvider>
  );
}

export default App;