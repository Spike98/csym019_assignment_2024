
package homework1;


public class Homework1 {

  
    public static void main(String[] args) {
        ///EMPLOYEES///
        
        Employee employee1 = new Employee ();
        Employee employee2 = new Employee ("Mary", 23232);
        Employee employee3 = new Employee ("John");
        Employee employee4 = new Employee ("Jerry", 22212, "Security");
        Employee employee5 = new Employee ("Nicole", 23212 , "HR");
        
        employee1.work();
        employee2.work();
        employee3.work();
        employee4.work();
        employee5.work();
        System.out.println("\n");
        
        /// MANAGERS ///
        Manager manager1 = new Manager ();
        Manager manager2 = new Manager ("Mike", 12345, "HR", 10);
        Manager manager3 = new Manager ("Jessica", 12344, "DevOps", 25);
        
        manager1.work();
        manager2.work();
        manager3.work();
        System.out.println("\n");
        
        /// ENGINEERS ///
        
        Engineer engineer1 = new Engineer ();
        Engineer engineer2 = new Engineer ("Tom", 0, "SOC", "Vulnerability Management");
        Engineer engineer3 = new Engineer ("Ashley", 33332,"Data Engineering Team","Data Analytics");
        Engineer engineer4 = new Engineer ("Jack", 11254, "DevOps","Front-End Engineering");
        
        engineer1.work();
        engineer2.work();
        engineer3.work();
        engineer4.work();
        
    }
    
    
    
}
