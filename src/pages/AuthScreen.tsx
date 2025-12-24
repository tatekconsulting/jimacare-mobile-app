import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { ChevronLeft, Eye, EyeOff, Mail, Lock, User, Phone } from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { toast } from "sonner";

export default function AuthScreen() {
  const navigate = useNavigate();
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  
  // Login form state
  const [loginEmail, setLoginEmail] = useState("");
  const [loginPassword, setLoginPassword] = useState("");
  
  // Register form state
  const [registerName, setRegisterName] = useState("");
  const [registerEmail, setRegisterEmail] = useState("");
  const [registerPhone, setRegisterPhone] = useState("");
  const [registerPassword, setRegisterPassword] = useState("");
  const [registerConfirmPassword, setRegisterConfirmPassword] = useState("");
  const [userType, setUserType] = useState<"client" | "provider">("client");

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    // Simulate login
    setTimeout(() => {
      setIsLoading(false);
      toast.success("Welcome back!");
      navigate("/");
    }, 1500);
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (registerPassword !== registerConfirmPassword) {
      toast.error("Passwords do not match");
      return;
    }
    
    setIsLoading(true);
    
    // Simulate registration
    setTimeout(() => {
      setIsLoading(false);
      toast.success("Account created successfully!");
      navigate("/");
    }, 1500);
  };

  return (
    <MobileLayout className="bg-gradient-to-b from-primary/5 to-background">
      {/* Header */}
      <header className="p-4 safe-area-top">
        <Button
          variant="ghost"
          size="icon-sm"
          onClick={() => navigate(-1)}
        >
          <ChevronLeft className="h-5 w-5" />
        </Button>
      </header>

      <main className="px-4 pb-8">
        {/* Logo & Title */}
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span className="text-2xl font-bold text-primary-foreground">J</span>
          </div>
          <h1 className="text-2xl font-bold text-foreground">JimaCare</h1>
          <p className="text-muted-foreground mt-1">Affordable & Quality Care</p>
        </div>

        {/* Auth Tabs */}
        <Tabs defaultValue="login" className="w-full">
          <TabsList className="grid w-full grid-cols-2 mb-6">
            <TabsTrigger value="login">Login</TabsTrigger>
            <TabsTrigger value="register">Register</TabsTrigger>
          </TabsList>

          {/* Login Tab */}
          <TabsContent value="login">
            <Card className="p-6">
              <form onSubmit={handleLogin} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="login-email">Email</Label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="login-email"
                      type="email"
                      placeholder="Enter your email"
                      value={loginEmail}
                      onChange={(e) => setLoginEmail(e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="login-password">Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="login-password"
                      type={showPassword ? "text" : "password"}
                      placeholder="Enter your password"
                      value={loginPassword}
                      onChange={(e) => setLoginPassword(e.target.value)}
                      className="pl-10 pr-10"
                      required
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    >
                      {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                    </button>
                  </div>
                </div>

                <Button
                  type="button"
                  variant="link"
                  className="p-0 h-auto text-primary"
                >
                  Forgot password?
                </Button>

                <Button
                  type="submit"
                  className="w-full"
                  size="lg"
                  disabled={isLoading}
                >
                  {isLoading ? "Signing in..." : "Sign In"}
                </Button>
              </form>
            </Card>
          </TabsContent>

          {/* Register Tab */}
          <TabsContent value="register">
            <Card className="p-6">
              {/* User Type Selection */}
              <div className="mb-6">
                <Label className="mb-3 block">I want to:</Label>
                <div className="grid grid-cols-2 gap-3">
                  <Button
                    type="button"
                    variant={userType === "client" ? "default" : "outline"}
                    className="h-auto py-4 flex flex-col gap-1"
                    onClick={() => setUserType("client")}
                  >
                    <span className="font-semibold">Find Care</span>
                    <span className="text-xs opacity-80">As a Client</span>
                  </Button>
                  <Button
                    type="button"
                    variant={userType === "provider" ? "default" : "outline"}
                    className="h-auto py-4 flex flex-col gap-1"
                    onClick={() => setUserType("provider")}
                  >
                    <span className="font-semibold">Provide Care</span>
                    <span className="text-xs opacity-80">As a Provider</span>
                  </Button>
                </div>
              </div>

              <form onSubmit={handleRegister} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="register-name">Full Name</Label>
                  <div className="relative">
                    <User className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="register-name"
                      type="text"
                      placeholder="Enter your full name"
                      value={registerName}
                      onChange={(e) => setRegisterName(e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="register-email">Email</Label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="register-email"
                      type="email"
                      placeholder="Enter your email"
                      value={registerEmail}
                      onChange={(e) => setRegisterEmail(e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="register-phone">Phone Number</Label>
                  <div className="relative">
                    <Phone className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="register-phone"
                      type="tel"
                      placeholder="Enter your phone number"
                      value={registerPhone}
                      onChange={(e) => setRegisterPhone(e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="register-password">Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="register-password"
                      type={showPassword ? "text" : "password"}
                      placeholder="Create a password"
                      value={registerPassword}
                      onChange={(e) => setRegisterPassword(e.target.value)}
                      className="pl-10 pr-10"
                      required
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    >
                      {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                    </button>
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="register-confirm">Confirm Password</Label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input
                      id="register-confirm"
                      type="password"
                      placeholder="Confirm your password"
                      value={registerConfirmPassword}
                      onChange={(e) => setRegisterConfirmPassword(e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                </div>

                <p className="text-xs text-muted-foreground">
                  By registering, you agree to our{" "}
                  <Button variant="link" className="p-0 h-auto text-primary text-xs">
                    Terms of Service
                  </Button>{" "}
                  and{" "}
                  <Button variant="link" className="p-0 h-auto text-primary text-xs">
                    Privacy Policy
                  </Button>
                </p>

                <Button
                  type="submit"
                  className="w-full"
                  size="lg"
                  disabled={isLoading}
                >
                  {isLoading ? "Creating account..." : "Create Account"}
                </Button>
              </form>
            </Card>
          </TabsContent>
        </Tabs>
      </main>
    </MobileLayout>
  );
}
