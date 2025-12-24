import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import HomeScreen from "./pages/HomeScreen";
import SearchScreen from "./pages/SearchScreen";
import ProviderProfile from "./pages/ProviderProfile";
import AuthScreen from "./pages/AuthScreen";
import FavoritesScreen from "./pages/FavoritesScreen";
import MessagesScreen from "./pages/MessagesScreen";
import ProfileScreen from "./pages/ProfileScreen";
import NotFound from "./pages/NotFound";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<HomeScreen />} />
          <Route path="/search" element={<SearchScreen />} />
          <Route path="/provider/:id" element={<ProviderProfile />} />
          <Route path="/auth" element={<AuthScreen />} />
          <Route path="/favorites" element={<FavoritesScreen />} />
          <Route path="/messages" element={<MessagesScreen />} />
          <Route path="/profile" element={<ProfileScreen />} />
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
